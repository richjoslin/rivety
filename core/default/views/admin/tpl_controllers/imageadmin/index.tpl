{capture name=pagetitle}{t}Manage Photos{/t}{/capture}
{capture name=css_urls}
	{$admin_theme_url}/tpl_controllers/imageadmin/index.css
{/capture}
{include file="file:$admin_theme_path/tpl_common/_header.tpl" pagetitle=$smarty.capture.pagetitle css_urls=$smarty.capture.css_urls}
<div id="main-column" class="full-width">
	<h3>{$smarty.capture.pagetitle}</h3>
	<h4>Important:</h4>
	<ul id="warnings">
		<li>Photo filenames should only contain letters, numbers, hyphens, underscores, and periods.</li>
		<li>Photo filenames containing anything other than those characters will have the offending characters automatically replaced with hyphens.</li>
		<li>Photos uploaded with the same filename as an existing photo will automatically overwrite the old photo.</li>
	</ul>
	<div id="photo-tabs">
		<ul>
			{foreach from=$uploads item=folder key=folder_name}
				<li><a href="#{$folder_name}">{$folder.friendly_name}</a></li>
			{/foreach}
		</ul>
		{foreach from=$uploads item=folder key=folder_name}
			<div id="{$folder_name}">
				<button class="upload">Upload {$folder.friendly_name} Photos</button>
				<button class="delete">Delete {$folder.friendly_name} Photos</button>
				<div class="gallery">
					<p style="margin: 20px 0;">
						Click a thumbnail to see the full-sized photo and filename.
					</p>
					{foreach from=$folder.filenames item=filename}
						<a href="/uploads/rivetycommon/{$folder_name}/{$filename}" target="_blank" title="{$filename}" class="fancybox">
							<img src="/displayimage/rivetycommon/{$folder_name}/80/0/0/png/{$filename}" alt="{$filename}" id="{$folder_name}_{$filename}" />
						</a>
					{/foreach}
				</div>
			</div>
		{/foreach}
	</div>
</div>

<div id="upload-photos-modal-form" title="Upload photos..." style="display: none;">
	<form action="/default/imageadmin/index/" method="POST" enctype="multipart/form-data">
		<fieldset>
			<input type="hidden" name="upload_to_folder" id="upload_to_folder" value="" />
			<input type="file" name="files_to_upload[]" id="files_to_upload" multiple="multiple" />
		</fieldset>
	</form>
</div>

<div id="delete-photos-modal-form" title="Delete photos..." style="display: none;">
	<p>
		Click on each photo you wish to delete and then click the Delete Photos button to permanently delete them. This action cannot be undone.
	</p>
	<form action="/default/imageadmin/index/" method="POST">
		<fieldset>
			<input type="hidden" name="delete_from_folder" id="delete_from_folder" value="" />
			<input type="hidden" name="files_to_delete" id="files_to_delete" value="" />
		</fieldset>
	</form>
</div>

{capture name=js_urls}
	{$admin_theme_url}/tpl_controllers/imageadmin/index.js
{/capture}
{include file="file:$admin_theme_path/tpl_common/_footer.tpl" js_urls=$smarty.capture.js_urls}
