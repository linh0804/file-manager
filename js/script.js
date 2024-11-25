// check cookie enable
function checkCookiesEnabled() {
  document.cookie = "fm_testcookie=1";

  if (document.cookie.indexOf("fm_testcookie=") == -1) {
    alert("Cookie bị tắt! File Manager sẽ không hoạt động đúng!");
  } else {
    // Xóa cookie vừa tạo
    document.cookie = "fm_testcookie=1; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
  }
}
checkCookiesEnabled();

// scroll
var scrollToTopTimeout = null;
var lastScrollTop = window.scrollY || document.documentElement.scrollTop;

var topButtons = document.querySelector("#scroll");
topButtons.style.transform = "rotate(180deg)";

topButtons.addEventListener("click", function () {
  var scroll = 0;

  if (topButtons.style.transform == "rotate(180deg)") {
    scroll = document.documentElement.scrollHeight;
  }

  window.scroll({
    top: scroll,
    left: 0,
    behavior: "smooth",
  });
});

window.addEventListener("scroll", function () {
  const scrollTopPosition = window.scrollY || document.documentElement.scrollTop;

  if (topButtons.style.display == "none") {
    topButtons.style.display = "block";
  }

  if (scrollTopPosition > lastScrollTop) {
    topButtons.style.transform = "rotate(180deg)";
  } else if (scrollTopPosition < lastScrollTop) {
    topButtons.style.transform = "rotate(0deg)";
  }

  lastScrollTop = scrollTopPosition <= 0 ? 0 : scrollTopPosition;

  clearTimeout(scrollToTopTimeout);
  scrollToTopTimeout = setTimeout(() => {
    topButtons.style.display = "none";
  }, 3000);
});

// autogrow
$('textarea[data-autoresize]').on('change input', function () {
  if (this.scrollHeight > this.clientHeight) {
    this.style.height = `${this.scrollHeight}px`;
  }
});

// copy
$('.copyButton').click(function (e) {
  e.preventDefault();

  let data = $(this).data('copy');

  navigator.clipboard
    .writeText(data)
    .then(function () {
      alert("Đã copy!");
    })
    .catch(function (err) {
      alert("Lỗi: ", err);
    });
});

// menu
document.addEventListener("click", function (e) {
  var targetId = e.target.id;
  if (targetId === "nav-menu" || targetId === "menuOverlay" || (document.body.classList.contains("has-menu") && e.target.closest(".menuToggle a:not(.noPusher)"))) {
    document.body.classList.toggle("has-menu");
  }
});
