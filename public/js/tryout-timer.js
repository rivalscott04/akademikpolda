// tryout-timer.js
let tryoutTimer = null;
let remainingTime = 0;
let totalDuration = 0;
let tryoutId = null;
let currentSoalId = null;

function initTryout(id, durationMinutes, soalId, remainingSeconds = null) {
    tryoutId = id;
    currentSoalId = soalId;
    totalDuration = durationMinutes * 60; // Convert to seconds

    // PERBAIKAN: Selalu gunakan remainingSeconds yang diberikan dari server
    // Jangan pernah menggunakan durasi penuh jika remainingSeconds sudah disediakan
    if (remainingSeconds !== null && remainingSeconds !== undefined) {
        remainingTime = Math.max(0, remainingSeconds); // Pastikan tidak negatif
    } else {
        remainingTime = totalDuration; // Fallback untuk kasus baru
    }

    console.log("Timer initialized:", {
        tryoutId: id,
        durationMinutes: durationMinutes,
        remainingSeconds: remainingTime,
        totalDuration: totalDuration,
        providedRemainingSeconds: remainingSeconds,
    });

    startTimer();
}

function startTimer() {
    // Clear any existing timer
    if (tryoutTimer) {
        clearInterval(tryoutTimer);
    }

    // Jika waktu sudah habis, langsung handle time up
    if (remainingTime <= 0) {
        handleTimeUp();
        return;
    }

    updateTimerDisplay();
    updateProgressBar();

    tryoutTimer = setInterval(function () {
        remainingTime--;

        updateTimerDisplay();
        updateProgressBar();
        updateTimerStyle();

        // Auto-save setiap 30 detik
        if (remainingTime % 30 === 0 && remainingTime > 0) {
            autoSave();
        }

        // Sync dengan server setiap 5 menit untuk memastikan waktu akurat
        if (remainingTime % 300 === 0 && remainingTime > 0) {
            syncTimeWithServer();
        }

        // Time's up
        if (remainingTime <= 0) {
            clearInterval(tryoutTimer);
            handleTimeUp();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const timerElement = document.getElementById("timer");
    if (!timerElement) return;

    const minutes = Math.floor(Math.max(0, remainingTime) / 60);
    const seconds = Math.max(0, remainingTime) % 60;

    const formattedTime = `${minutes.toString().padStart(2, "0")}:${seconds
        .toString()
        .padStart(2, "0")}`;
    timerElement.textContent = formattedTime;
}

function updateProgressBar() {
    const progressBar = document.getElementById("timer-progress");
    if (!progressBar) return;

    const percentage = Math.max(0, (remainingTime / totalDuration) * 100);
    progressBar.style.width = percentage + "%";

    // Change color based on remaining time
    progressBar.className = "progress-bar";
    if (percentage > 50) {
        progressBar.classList.add("bg-success");
    } else if (percentage > 25) {
        progressBar.classList.add("bg-warning");
    } else {
        progressBar.classList.add("bg-danger");
    }
}

function updateTimerStyle() {
    const timerDisplay = document.querySelector(".timer-display");
    if (!timerDisplay) return;

    // Remove existing classes
    timerDisplay.classList.remove("warning", "danger");

    // Add warning/danger classes based on remaining time
    if (remainingTime <= 60) {
        // 1 minute
        timerDisplay.classList.add("danger");
    } else if (remainingTime <= 300) {
        // 5 minutes
        timerDisplay.classList.add("warning");
    }
}

function autoSave() {
    // Only auto-save if there's a selected answer and saveAnswer function exists
    const form = document.querySelector(".options-container");
    if (!form) return;

    const checkedInputs = form.querySelectorAll("input:checked");
    if (checkedInputs.length > 0 && typeof window.saveAnswer === "function") {
        console.log("Auto-saving answer...");
        window.saveAnswer(true); // Pass true to indicate this is auto-save
    }
}

// PERBAIKAN: Tambahkan fungsi untuk sync waktu dengan server
function syncTimeWithServer() {
    if (!tryoutId) return;

    // Hanya sync jika ada endpoint untuk mendapatkan remaining time
    fetch(`/user/tryout/${tryoutId}/remaining-time`, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN":
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") || "",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success && data.remainingTime !== undefined) {
                const serverRemainingTime = Math.max(0, data.remainingTime);

                // Jika perbedaan waktu terlalu besar (lebih dari 5 detik), sync dengan server
                if (Math.abs(remainingTime - serverRemainingTime) > 5) {
                    console.log("Syncing time with server:", {
                        clientTime: remainingTime,
                        serverTime: serverRemainingTime,
                    });
                    remainingTime = serverRemainingTime;

                    if (remainingTime <= 0) {
                        clearInterval(tryoutTimer);
                        handleTimeUp();
                    }
                }
            }
        })
        .catch((error) => {
            console.log("Time sync failed:", error);
            // Tidak perlu menghentikan timer jika sync gagal
        });
}

function handleTimeUp() {
    console.log("Time is up!");
    remainingTime = 0; // Pastikan waktu adalah 0

    // Update display terakhir kali
    updateTimerDisplay();
    updateProgressBar();

    // Call the global onTimeUp function if it exists
    if (typeof window.onTimeUp === "function") {
        window.onTimeUp();
    } else {
        // Fallback behavior
        alert("Waktu tryout telah habis!");
        window.location.href = `/user/tryout/${tryoutId}/finish`;
    }
}

function pauseTimer() {
    if (tryoutTimer) {
        clearInterval(tryoutTimer);
        console.log("Timer paused");
    }
}

function resumeTimer() {
    if (remainingTime > 0) {
        startTimer();
        console.log("Timer resumed");
    }
}

function getRemainingTime() {
    return remainingTime;
}

function getTotalDuration() {
    return totalDuration;
}

// PERBAIKAN: Tambahkan fungsi untuk set remaining time dari luar
function setRemainingTime(seconds) {
    remainingTime = Math.max(0, seconds);
    console.log("Remaining time set to:", remainingTime);
}

// Handle page visibility change (optional: pause/resume timer)
document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
        console.log("Page hidden");
        // Bisa pause timer di sini jika diperlukan
        // pauseTimer();
    } else {
        console.log("Page visible");
        // Resume timer dan sync dengan server
        // resumeTimer();
        if (remainingTime > 0) {
            syncTimeWithServer();
        }
    }
});

// Handle storage untuk persist timer state (opsional)
function saveTimerState() {
    if (tryoutId && remainingTime > 0) {
        const timerState = {
            tryoutId: tryoutId,
            remainingTime: remainingTime,
            timestamp: Date.now(),
        };
        sessionStorage.setItem(
            "tryout_timer_state",
            JSON.stringify(timerState)
        );
    }
}

function loadTimerState() {
    try {
        const saved = sessionStorage.getItem("tryout_timer_state");
        if (saved) {
            const state = JSON.parse(saved);
            if (state.tryoutId === tryoutId) {
                // Hitung waktu yang telah berlalu sejak save
                const elapsed = Math.floor(
                    (Date.now() - state.timestamp) / 1000
                );
                const adjustedTime = Math.max(0, state.remainingTime - elapsed);

                if (adjustedTime > 0 && adjustedTime < remainingTime) {
                    remainingTime = adjustedTime;
                    console.log(
                        "Timer state loaded from storage:",
                        remainingTime
                    );
                }
            }
        }
    } catch (error) {
        console.log("Failed to load timer state:", error);
    }
}

// Save timer state setiap 10 detik
setInterval(saveTimerState, 10000);

// Handle beforeunload to warn user
let allowNavigation = false;
let isInternalNavigation = false;
let hasMarkedQuestions = false;

window.addEventListener("beforeunload", function (e) {
    // Save timer state sebelum leave
    saveTimerState();

    // Don't show warning if navigation is allowed, time is up, or it's internal navigation
    if (allowNavigation || remainingTime <= 0 || isInternalNavigation) {
        return;
    }

    // Check if there are marked questions - if yes, allow navigation without warning
    if (hasMarkedQuestions) {
        return;
    }

    // Only show warning for external navigation when no marked questions
    const message =
        "Tryout sedang berlangsung. Apakah Anda yakin ingin meninggalkan halaman?";
    e.returnValue = message;
    return message;
});

// Allow navigation for internal tryout links
function allowTryoutNavigation() {
    allowNavigation = true;
    isInternalNavigation = true;

    // Save state sebelum navigasi internal
    saveTimerState();

    // Reset flag after a short delay
    setTimeout(() => {
        allowNavigation = false;
        isInternalNavigation = false;
    }, 1000);
}

// Detect if user is navigating within the same domain/tryout
function isInternalTryoutNavigation(url) {
    if (!url) return false;

    const currentOrigin = window.location.origin;
    const currentPath = window.location.pathname;

    // Check if it's same origin and contains tryout path
    return (
        url.startsWith(currentOrigin) &&
        (url.includes("/tryout/") || url.includes("question="))
    );
}

// Function to update marked questions status
function updateMarkedQuestionsStatus() {
    const markedQuestions = document.querySelectorAll('.question-number.marked');
    hasMarkedQuestions = markedQuestions.length > 0;
    console.log('Marked questions status updated:', hasMarkedQuestions);
}

// Function to check if there are marked but unanswered questions
function checkMarkedUnansweredQuestions() {
    const markedQuestions = document.querySelectorAll('.question-number.marked');
    const unansweredQuestions = document.querySelectorAll('.question-number.unanswered');
    
    let markedUnanswered = 0;
    markedQuestions.forEach(marked => {
        if (marked.classList.contains('unanswered')) {
            markedUnanswered++;
        }
    });
    
    return {
        totalMarked: markedQuestions.length,
        markedUnanswered: markedUnanswered,
        hasMarkedUnanswered: markedUnanswered > 0
    };
}

// Expose functions globally
window.initTryout = initTryout;
window.pauseTimer = pauseTimer;
window.resumeTimer = resumeTimer;
window.getRemainingTime = getRemainingTime;
window.getTotalDuration = getTotalDuration;
window.setRemainingTime = setRemainingTime;
window.allowTryoutNavigation = allowTryoutNavigation;
window.isInternalTryoutNavigation = isInternalTryoutNavigation;
window.syncTimeWithServer = syncTimeWithServer;
window.updateMarkedQuestionsStatus = updateMarkedQuestionsStatus;
window.checkMarkedUnansweredQuestions = checkMarkedUnansweredQuestions;
