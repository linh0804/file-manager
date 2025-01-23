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
function toggleMenu() {
    document.body.classList.toggle("has-menu");
}

document.addEventListener("click", function (e) {
  var targetId = e.target.id;
  if (targetId === "nav-menu" || targetId === "menuOverlay" || (document.body.classList.contains("has-menu") && e.target.closest(".menuToggle a:not(.noPusher)"))) {
    document.body.classList.toggle("has-menu");
  }
});

function redirect(url) {
  window.location.href = url;
}

function fileAjax(data, success) {
  $.ajax({
    url: 'api.file.php',
    method: 'post',
    data: data,
    success: success,
    error: function () {
      alert("Lỗi server!");
    }
  });
}

function fileAjaxDelete(element) {
  const data = $(element).data();

  if (!confirm(`Xác nhận xóa "${data.path}"?`)) {
    return;
  }

  fileAjax(data, function (res) {
    if (res.msg) {
      alert(res.msg);
    }

    if (res.redirect) {
      redirect(res.redirect);
    }
  });
}

$(document).ready(function () {
    let startX = 0;
    let startY = 0;
    let isSwiping = false;

    $(document).on('touchstart', function (e) {
        const target = e.target;

        // Bỏ qua nếu vuốt trên input, textarea, hoặc các thành phần có thể nhập liệu
        if ($(target).is('input, textarea, select, [contenteditable]')) {
            isSwiping = false;
            return;
        }

        const touch = e.originalEvent.touches[0];
        startX = touch.clientX;
        startY = touch.clientY;
        isSwiping = true;
    });

    $(document).on('touchmove', function (e) {
        if (!isSwiping) return;

        const touch = e.originalEvent.touches[0];
        const diffX = touch.clientX - startX;
        const diffY = touch.clientY - startY;

        // Ngăn cuộn dọc nếu vuốt chủ yếu theo phương ngang
        if (Math.abs(diffX) > Math.abs(diffY)) {
            e.preventDefault();
        }
    });

    $(document).on('touchend', function (e) {
        if (!isSwiping) return;

        const touch = e.originalEvent.changedTouches[0];
        const diffX = touch.clientX - startX;
        const diffY = touch.clientY - startY;
        isSwiping = false;

        // Kiểm tra vuốt từ trái sang phải
        if (Math.abs(diffX) > 50 && Math.abs(diffY) < 30 && diffX > 0) {
            console.log('Vuốt từ trái sang phải!');
            toggleMenu();
        }
    });
});
