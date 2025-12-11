<footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Peta Kasus 2025</span>
                    </div>
                </div>
            </footer>
            </div>
        </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    <script>
    $(document).ready(function() {
        // Fungsi untuk memuat halaman tanpa reload
        function loadContent(url) {
            // Tampilkan loading (opsional, bisa diganti spinner)
            $('#content > .container-fluid').css('opacity', '0.5');

            // Ambil konten dari URL tujuan
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Ambil hanya bagian .container-fluid dari halaman tujuan
                    var newContent = $(response).find('.container-fluid').html();
                    
                    if (!newContent) {
                        // Jika tidak ada container-fluid (misal halaman login/error), redirect biasa
                        window.location.href = url;
                        return;
                    }

                    // Ganti konten lama dengan yang baru
                    $('#content > .container-fluid').html(newContent).css('opacity', '1');
                    
                    // Update URL di browser tanpa reload (agar terlihat rapi)
                    // window.history.pushState(null, null, url); 
                    // Baris di atas saya matikan agar sesuai permintaan "tetap di alamat itu"
                    
                    // Bersihkan elemen yang tidak perlu (misal navbar frontend jika masuk ke admin)
                    $('#content .navbar-custom').remove(); 
                    $('#content footer').remove();
                },
                error: function() {
                    alert('Gagal memuat halaman.');
                    $('#content > .container-fluid').css('opacity', '1');
                }
            });
        }

        // 1. TANGKAP KLIK LINK DI SIDEBAR & KONTEN
        $(document).on('click', 'a.nav-link, a.collapse-item, a.btn-link, .container-fluid a', function(e) {
            var url = $(this).attr('href');

            // Abaikan link hash (#), javascript:, logout, atau link eksternal
            if (!url || url === '#' || url.startsWith('javascript:') || url.includes('logout.php')) {
                return; 
            }
            
            // Abaikan tombol hapus yang menggunakan onclick confirm bawaan
            if ($(this).attr('onclick')) {
                return;
            }

            e.preventDefault(); // Cegah reload browser
            loadContent(url);   // Panggil fungsi ajax
        });

        // 2. TANGKAP PENGIRIMAN FORM (Simpan/Update Data)
        $(document).on('submit', 'form', function(e) {
            e.preventDefault(); // Cegah reload saat submit form
            
            var form = $(this);
            var url = form.attr('action') || window.location.href; // Gunakan action form atau URL saat ini
            var formData = new FormData(this);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Cek jika response berisi script redirect (kasus hapus/simpan)
                    if (response.includes('window.location')) {
                        // Eksekusi script alert dari PHP (misal: Data Berhasil Disimpan)
                        var tempDiv = $('<div>').html(response);
                        var scriptContent = tempDiv.find('script').html();
                        if(scriptContent) {
                            // Jalankan alert
                            eval(scriptContent.replace(/window\.location.*/, "")); 
                            // Muat ulang halaman index/kasus setelah simpan
                            loadContent('../kasus.php'); 
                        } else {
                           // Jika tidak ada script, muat ulang halaman dashboard default
                           loadContent('index.php');
                        }
                    } else {
                        // Jika tidak ada redirect, ganti konten dengan hasil (misal error form)
                        var newContent = $(response).find('.container-fluid').html();
                        if (newContent) {
                            $('#content > .container-fluid').html(newContent);
                        } else {
                             loadContent('index.php');
                        }
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan data.');
                }
            });
        });
    });
    </script>

</body>
</html>