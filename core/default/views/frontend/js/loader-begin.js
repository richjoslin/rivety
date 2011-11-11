// don't use jQuery here because jQuery hasn't been loaded yet

if (document.getElementById('options')) document.getElementById('options').style.display = 'none';

var mainColumn = document.getElementById('main-column');
if (mainColumn)
{
	// TODO: this breaks the layout sometimes
	// mainColumn.style.height = 0;
	// mainColumn.style.overflow = 'hidden';
}
