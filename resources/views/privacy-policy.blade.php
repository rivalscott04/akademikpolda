<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Police Academy Test Prep</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <style>
        .policy-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 30px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .policy-section h2 {
            color: #1ab394;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        .policy-list {
            list-style-type: none;
            padding-left: 0;
        }

        .policy-list li {
            margin-bottom: 15px;
            padding-left: 25px;
            position: relative;
        }

        .policy-list li:before {
            content: "•";
            color: #1ab394;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .contact-info {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .btn-info {
            background-color: #1ab394;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            max-width: 100%;
            margin: 30px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Syarat dan Ketentuan</a>
        </div>
    </nav>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Back Button -->
                <a href="\" class="btn btn-info mb-5">&larr; Kembali</a>

                <!-- Video Section -->
                <div class="policy-section">
                    <h2>Video Panduan</h2>
                    <p class="lead">Tonton video panduan ini untuk memahami lebih lanjut tentang layanan kami:</p>

                    <div class="video-container">
                        <!-- Replace VIDEO_ID with your actual YouTube video ID -->
                        <iframe src="https://www.youtube.com/embed/wU8hKwhPF_k?si=R1mkGo6BJVft-wpN"
                            title="YouTube video player"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>

                <!-- Privacy Policy Section -->
                <div class="policy-section">
                    <h2>Lupa password</h2>
                    <p class="lead">Ikuti langkah ini jika anda lupa password.</p>
                    <ol class="policy-list">
                        <li style="font-size: 18px">Tekan tombol "Lupa password" seperti yang tertera di gambar <br><br>
                            <img src="{{ asset('img/lupa password.jpeg') }}" alt="" class="img-fluid">
                        </li>
                        <li style="font-size: 18px">Kemudian isi alamat email beserta password baru anda, dan tekan
                            tombol "Reset Password".<br><br>
                            <img src="{{ asset('img/lupa password 2.png') }}" alt="" class="img-fluid">
                        </li>

                    </ol>
                </div>

                <!-- Privacy Policy Section -->
                <div class="policy-section">
                    <h2>Privacy Policy</h2>
                    <p class="lead">Privasi Anda penting bagi kami. Kebijakan Privasi ini menjelaskan bagaimana kami
                        mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi Anda.</p>
                    <ol class="policy-list">
                        <li><strong>Pengumpulan Informasi:</strong> Kami dapat mengumpulkan informasi pribadi seperti
                            nama, alamat email, dan hasil tes untuk meningkatkan pengalaman pengguna Anda.</li>
                        <li><strong>Penggunaan Informasi:</strong> Informasi yang dikumpulkan akan digunakan untuk
                            personalisasi pengalaman persiapan tes Anda, memberikan umpan balik, dan meningkatkan
                            layanan kami.</li>
                        <li><strong>Keamanan Data:</strong> Kami menerapkan langkah-langkah yang tepat untuk melindungi
                            informasi pribadi Anda dari akses dan penggunaan yang tidak sah.</li>
                        <li><strong>Pengungkapan Pihak Ketiga:</strong> Kami tidak menjual, memperdagangkan, atau
                            mentransfer informasi pribadi Anda kepada pihak luar.</li>
                        <li><strong>Perubahan Kebijakan Privasi:</strong> Kami dapat memperbarui Kebijakan Privasi ini
                            dari waktu ke waktu. Setiap perubahan akan diposting di halaman ini.</li>
                    </ol>
                </div>

                <!-- Return & Refund Policy Section -->
                <div class="policy-section">
                    <h2>Kebijakan Pengembalian & Refund</h2>
                    <ol class="policy-list">
                        <li><strong>Kelayakan:</strong> Kebijakan pengembalian dan refund kami berlaku selama 30 hari
                            dari tanggal pembelian.</li>
                        <li><strong>Proses:</strong> Untuk memulai pengembalian atau refund, silakan hubungi tim
                            dukungan kami dengan detail pesanan Anda.</li>
                        <li><strong>Refund:</strong> Setelah kami menerima permintaan Anda, kami akan memproses refund
                            Anda dalam waktu 7-10 hari kerja.</li>
                        <li><strong>Pengecualian:</strong> Produk digital tidak dapat dikembalikan kecuali ada masalah
                            signifikan dengan konten atau pengiriman.</li>
                    </ol>
                </div>

                <!-- Contact Section -->
                <div class="contact-info">
                    <h2>Hubungi Kami</h2>
                    <p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi kami:</p>
                    <address>
                        <strong>Alamat:</strong><br>
                        Jl. Merdeka Raya Jempong Baru<br><br>
                        <strong>Email:</strong><br>
                        <a href="mailto:sasambosolusidigital@gmail.com">sasambosolusidigital@gmail.com</a>
                    </address>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <strong>Copyright</strong> Mahir Akademik &copy; {{ date('Y') }}
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
