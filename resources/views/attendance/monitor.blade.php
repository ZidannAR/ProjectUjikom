<!DOCTYPE html>
<html>
<head>
    <title>Monitor Absensi</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column; font-family: sans-serif;">

    <h2>Scan QR untuk Absen</h2>
    
    <div id="qrcode" style="margin: 20px;"></div>
    
    <p>QR diperbarui dalam: <span id="timer">5</span> detik</p>

    <script>
        // Inisialisasi library QR
        const qrElement = document.getElementById("qrcode");
        const qrcode = new QRCode(qrElement, {
            width: 250,
            height: 250
        });

        function updateQR() {
            // Panggil API Laravel yang tadi kita perbaiki
            fetch('/api/get-new-token')
                .then(res => res.json())
                .then(data => {
                    // Update gambar QR dengan token baru
                    qrcode.makeCode(data.token);
                    console.log("Token Updated: " + data.token);
                })
                .catch(err => console.error("Gagal ambil token:", err));
        }

        let timeLeft = 5;
        setInterval(() => {
            timeLeft--;
            document.getElementById('timer').innerText = timeLeft;
            if(timeLeft <= 0) {
                updateQR(); // Ambil token baru setiap 5 detik
                timeLeft = 5;
            }
        }, 1000);

        // Jalankan saat pertama kali halaman dibuka
        updateQR();
    </script>
</body>
</html>