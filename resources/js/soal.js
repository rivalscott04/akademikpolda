let currentSet = 0;
let waktuTersisa = 60;
let skorBenar = 0;
let skorSalah = 0;
let timerInterval;
let detailJawaban = [];

async function getNextSoal() {
    try {
        const response = await fetch('/tes-kecermatan/next-soal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ current_set: currentSet })
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentSet = data.data.set_number;
            kolomMerah = data.data.soal;
            updateKolomMerah();
            generateKolomBiru();
            
            if (!data.data.is_last) {
                showTransitionPopup();
            } else {
                selesaiTes();
            }
        } else {
            selesaiTes();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function showTransitionPopup() {
    clearInterval(timerInterval);
    waktuTersisa = 60;
    const popup = document.getElementById('popup');
    const countdown = document.getElementById('countdown');
    let countdownValue = 5;

    popup.style.display = 'flex';
    const countdownInterval = setInterval(() => {
        countdownValue--;
        countdown.textContent = countdownValue;
        if (countdownValue === 0) {
            clearInterval(countdownInterval);
            popup.style.display = 'none';
            mulaiTimer();
        }
    }, 1000);
}

async function selesaiTes() {
    clearInterval(timerInterval);
    nonaktifkanTombol();
    
    try {
        await simpanHasil();
        tampilkanHasil();
    } catch (error) {
        console.error('Error menyimpan hasil:', error);
    }
}

async function simpanHasil() {
    try {
        const response = await fetch('/tes-kecermatan/simpan-hasil', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                user_id: userId, // Pastikan variable userId tersedia
                skor_benar: skorBenar,
                skor_salah: skorSalah,
                waktu_total: (10 * 60) - waktuTersisa,
                detail_jawaban: detailJawaban
            })
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Event listener untuk tombol
['A', 'B', 'C', 'D', 'E'].forEach((huruf) => {
    document.getElementById(`btn-${huruf}`).addEventListener('click', () => {
        const jawaban = {
            set: currentSet,
            huruf_tebakan: huruf,
            huruf_benar: hurufHilang.opsi,
            is_benar: hurufHilang.opsi === huruf,
            waktu: 60 - waktuTersisa
        };
        
        detailJawaban.push(jawaban);
        
        if (jawaban.is_benar) {
            skorBenar++;
        } else {
            skorSalah++;
        }
        
        generateKolomBiru();
    });
});

// Inisialisasi
getNextSoal();
