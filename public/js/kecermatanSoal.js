class KecermatanSoal {
    constructor(config) {
        this.config = config;
        // FIX: Handle cardId properly - convert invalid values to null
        this.cardId = this.validateCardId(config.cardId);
        this.currentSet = 0;
        this.kolomMerah = [];
        this.kolomBiru = [];
        this.hurufHilang = null;
        this.waktuTersisa = 60;
        this.skorBenar = 0;
        this.skorSalah = 0;
        this.timerInterval = null;
        this.detailJawaban = [];
        this.totalSets = 10;
        this.allQuestions = [];
    }

    // FIX: Validate cardId to handle invalid values
    validateCardId(cardId) {
        if (
            !cardId ||
            cardId === "?" ||
            cardId === "" ||
            cardId === "undefined" ||
            cardId === "null"
        ) {
            return null;
        }
        return cardId;
    }

    init() {
        console.log("Initializing KecermatanSoal with cardId:", this.cardId);
        this.getQuestionsFromURL();
        this.initializeButtons();
        this.getNextSoal().then(() => {
            this.updateKolomMerah();
            this.generateKolomBiru();
            this.mulaiTimer();
        });
    }

    // Get questions from URL parameters
    getQuestionsFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const questions = urlParams.getAll("questions[]");
        this.allQuestions = questions.map((chars, index) => {
            return Array.from(chars).map((char, i) => ({
                huruf: char,
                opsi: String.fromCharCode(65 + i),
            }));
        });
    }

    async getNextSoal() {
        try {
            this.currentSet++;
            if (this.currentSet > this.totalSets) {
                return true;
            }

            this.kolomMerah = this.allQuestions[this.currentSet - 1] || [];
            const currentSetElement = document.getElementById("current-set");
            if (currentSetElement) {
                currentSetElement.textContent = this.currentSet;
            }

            return false;
        } catch (error) {
            console.error("Error getting next question:", error);
            return true;
        }
    }

    acakArray(array) {
        const arrayBaru = [...array];
        for (let i = arrayBaru.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arrayBaru[i], arrayBaru[j]] = [arrayBaru[j], arrayBaru[i]];
        }
        return arrayBaru;
    }

    generateKolomBiru() {
        const shuffled = this.acakArray(this.kolomMerah);
        this.kolomBiru = shuffled.slice(0, 4);
        this.hurufHilang = shuffled[4];
        this.updateKolomBiru();
    }

    updateKolomMerah() {
        const row = document.getElementById("kolom-merah");
        if (!row) return;

        row.innerHTML = "";
        this.kolomMerah.forEach((item) => {
            const td = document.createElement("td");
            td.className = "karakter";
            td.textContent = item.huruf;
            row.appendChild(td);
        });
    }

    updateKolomBiru() {
        const container = document.getElementById("kolom-biru");
        if (!container) return;

        container.innerHTML = "";
        this.kolomBiru.forEach((item) => {
            const div = document.createElement("div");
            div.className = "answer-box";
            div.textContent = item.huruf;
            container.appendChild(div);
        });
    }

    mulaiTimer() {
        clearInterval(this.timerInterval);
        this.waktuTersisa = 60;
        const timerElement = document.getElementById("timer");
        if (timerElement) {
            timerElement.textContent = this.waktuTersisa;
        }

        this.timerInterval = setInterval(async () => {
            if (this.waktuTersisa > 0) {
                this.waktuTersisa--;
                if (timerElement) {
                    timerElement.textContent = this.waktuTersisa;
                }
            } else {
                clearInterval(this.timerInterval);
                if (this.currentSet < this.totalSets) {
                    const isLastSet = await this.transisiKeSetBerikutnya();
                    if (isLastSet) {
                        this.selesaiTes();
                    }
                } else {
                    this.selesaiTes();
                }
            }
        }, 1000);
    }

    async transisiKeSetBerikutnya() {
        clearInterval(this.timerInterval);

        if (this.currentSet >= this.totalSets) {
            return true;
        }

        return new Promise((resolve) => {
            let countdownInterval;
            Swal.fire({
                title: "Persiapan Soal Berikutnya",
                html: "Soal berikutnya akan dimulai dalam <b></b> detik.",
                timer: 3000,
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    const timer = Swal.getPopup().querySelector("b");
                    countdownInterval = setInterval(() => {
                        timer.textContent = Math.ceil(
                            Swal.getTimerLeft() / 1000
                        );
                    }, 100);
                },
                willClose: () => {
                    clearInterval(countdownInterval);
                },
            }).then(async () => {
                const isLast = await this.getNextSoal();
                if (!isLast) {
                    this.updateKolomMerah();
                    this.generateKolomBiru();
                    this.mulaiTimer();
                }
                resolve(isLast);
            });
        });
    }

    selesaiTes() {
        clearInterval(this.timerInterval);

        const baseUrl = window.location.origin;

        // FIX: Prepare data with validated cardId
        const dataToSend = {
            user_id: this.config.userId,
            skor_benar: this.skorBenar,
            skor_salah: this.skorSalah,
            waktu_total: this.totalSets * 60 - this.waktuTersisa,
            detail_jawaban: this.detailJawaban,
        };

        console.log("Data being sent to server:", dataToSend);

        fetch(this.config.routes.simpanHasil, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.config.csrfToken,
            },
            body: JSON.stringify(dataToSend),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((result) => {
                if (result.success) {
                    Swal.fire({
                        title: "Tes Selesai!",
                        html: `
                            <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <span class="score-badge benar">Benar: ${
                                        this.skorBenar
                                    }</span>
                                </div>
                                <div>
                                    <span class="score-badge salah">Salah: ${
                                        this.skorSalah
                                    }</span>
                                </div>
                            </div>
                            <div style="margin-top: 1rem;">
                                <strong>Total Skor: ${
                                    this.skorBenar - this.skorSalah
                                }</strong>
                            </div>
                        `,
                        icon: "success",
                        showCloseButton: true,
                        confirmButtonText: "OK",
                        width: "600px",
                        allowOutsideClick: false,
                    }).then(() => {
                        if (this.cardId) {
                            window.location.href = `${baseUrl}/tryout?type=lengkap`;
                        } else {
                            window.location.href = `${baseUrl}/tes-kecermatan/riwayat/${this.config.userId}`;
                        }
                    });
                } else {
                    throw new Error(result.message || "Failed to save results");
                }
            })
            .catch((error) => {
                console.error("Error saving results:", error);
                Swal.fire({
                    title: "Error!",
                    text: "Gagal menyimpan hasil tes: " + error.message,
                    icon: "error",
                    confirmButtonText: "OK",
                });
            });
    }

    // Initialize button event listeners
    initializeButtons() {
        ["A", "B", "C", "D", "E"].forEach((huruf) => {
            const button = document.getElementById(`btn-${huruf}`);
            if (button) {
                button.addEventListener("click", () => {
                    const isBenar = this.hurufHilang.opsi === huruf;
                    if (isBenar) {
                        this.skorBenar++;
                    } else {
                        this.skorSalah++;
                    }

                    this.detailJawaban.push({
                        set: this.currentSet,
                        jawaban: huruf,
                        benar: isBenar,
                        waktu: 60 - this.waktuTersisa,
                        soal_asli: this.kolomMerah
                            .map((item) => item.huruf)
                            .join(""),
                        soal_acak:
                            this.kolomBiru.map((item) => item.huruf).join("") +
                            this.hurufHilang.huruf,
                        huruf_hilang: this.hurufHilang.huruf,
                        posisi_huruf_hilang: this.hurufHilang.opsi,
                    });

                    this.generateKolomBiru();
                });
            }
        });
    }
}
