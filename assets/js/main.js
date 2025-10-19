// KODE UNTUK DROPDOWN MENU PROFIL
document.addEventListener("DOMContentLoaded", function () {
  const userMenuTrigger = document.querySelector(".user-menu-trigger");
  const dropdownMenu = document.querySelector(".dropdown-menu");
  if (userMenuTrigger && dropdownMenu) {
    userMenuTrigger.addEventListener("click", function (event) {
      dropdownMenu.classList.toggle("active");
      event.stopPropagation();
    });
    window.addEventListener("click", function () {
      if (dropdownMenu.classList.contains("active")) {
        dropdownMenu.classList.remove("active");
      }
    });
  }
});
// elemen jam dinding
const countdownElement = document.getElementById("countdown");

if (countdownElement) {
  // Ambil waktu selesai lelang dari atribut data-end-time
  const endTime = new Date(countdownElement.dataset.endTime).getTime();

  // Perbarui hitungan mundur setiap 1 detik
  const timer = setInterval(function () {
    // Dapatkan waktu saat ini
    const now = new Date().getTime();

    // Cari selisih antara waktu selesai dan waktu sekarang
    const distance = endTime - now;

    // Jika waktu sudah habis
    if (distance < 0) {
      clearInterval(timer);
      countdownElement.innerHTML = "Upss, kamu telat Lelangnya sudah selesai!";
      return;
    }

    // Hitung hari, jam, menit, detik
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor(
      (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    );
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Tampilkan hasilnya di dalam elemen
    countdownElement.innerHTML =
      days +
      " hari " +
      hours +
      " jam " +
      minutes +
      " menit " +
      seconds +
      " detik ";
  }, 1000);
}

