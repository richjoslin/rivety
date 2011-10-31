// don't use jQuery here because jQuery hasn't been loaded yet
document.getElementById('options').style.display = 'none';
var mainColumn = document.getElementById('main-column');
mainColumn.style.height = 0;
mainColumn.style.overflow = 'hidden';
