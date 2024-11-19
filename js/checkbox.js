function onCheckItem() {
  for (let i = 0; i < document.form.elements.length; ++i) {
    if (document.form.elements[i].type === "checkbox") {
      document.form.elements[i].checked = document.form.all.checked === true;
    }
  }
}
