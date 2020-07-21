<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2019 - 2020 openROOM. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// use_xml: Usar XML si/no
// xml_file: nombre del archivo XML
// open_subfolders: Cargar contenido de las subcarpetas si/no
// mini_folder: Carpeta fotos miniatura
// maxi_folder: Carpeta fotos ampliación
// open_modal: Abrir en modal si/no

$orGallery_use_xml = $params->get('use_xml') == 1 ? true : false;
$open_modal = $params->get('open_modal') == 1 ? true : false;

$orGallery_fotos = array();
if($orGallery_use_xml)
{
	$orGallery_xml_file = $params->get('xml_file');
	// Create a JSimpleXML object and then parse the file
	$orGallery_xml = simplexml_load_file(JURI::root() .$orGallery_xml_file, "SimpleXMLElement");
	//print_r($orGallery_xml);
	if (!empty($orGallery_xml->foto))
	{
		// We can now step through each element of the file
		$orGallery_xml_id = 0;
		foreach( $orGallery_xml->foto as $orGallery_foto )
		{
		   $orGallery_fotos[$orGallery_xml_id]['mini'] = $orGallery_foto->mini;
		   $orGallery_fotos[$orGallery_xml_id]['maxi'] = $orGallery_foto->maxi;
		   $orGallery_fotos[$orGallery_xml_id]['title'] = isset($orGallery_foto->title) ? $orGallery_foto->title : "";
		   $orGallery_fotos[$orGallery_xml_id]['xml_title'] = isset($orGallery_foto->xml_title) ? $orGallery_foto->xml_title : "";
		   $orGallery_fotos[$orGallery_xml_id]['xml_link'] = isset($orGallery_foto->xml_link) ? $orGallery_foto->xml_link : "";
		   $orGallery_fotos[$orGallery_xml_id]['xml_link_text'] = isset($orGallery_foto->xml_link_text) ? $orGallery_foto->xml_link_text : "";
		   $orGallery_xml_id++;
		}
	}else{
		//echo $orGallery_xml_file;
		$orGallery_use_xml = false;
		}
}
if(!$orGallery_use_xml)
{
	$open_subfolders = $params->get('open_subfolders') == 1 ? true : false;
	$alt_generico = $params->get('alt_generico');
	
	/** Limpiamos la URL para evitar // o / al inicio **/
	$or_GalleryMin_dir = orGallery_dirFilter($params->get('mini_folder'));
	$or_GalleryMax_dir = orGallery_dirFilter($params->get('maxi_folder'));
	$orGallery_fotos = orGallery_listDirPhotos($or_GalleryMin_dir, $or_GalleryMax_dir, $open_subfolders);
	
}

$orTemplate_xml = JFactory::getXML(JPATH_SITE .'/templates/openroom/templateDetails.xml');
$orTemplate_version = orTemplate_v4gallery((string)$orTemplate_xml->version);


if(!$orTemplate_version){
?>
<script>
jQuery(document).ready(function()
{
	jQuery('.or_gallery_image').on('click', function(){
		// Comprobamos si hay modal abierto
		if(!jQuery('body').hasClass('overflow-hiden'))
		{
			//var padre = jQuery(this).closest('.or_gallery_content');
			// comprobamos si tiene una etiqueta <a>			
			if(jQuery('a', this).length == 0)
			{
				var current = jQuery(this);
				var mini = jQuery('img', current);
				var altTxt = mini.attr('alt');
				var maxi = current.attr('rel-image');
				var modal = jQuery('.or_galleryModal');
				var xmlData = false;
				if(current.attr('rel-xmltitle') != "" && current.attr('rel-xmltitle') != undefined)
				{
					altTxt = current.attr('rel-xmltitle');
					xmlData = true;
				}
				jQuery('body').addClass('overflow-hiden');
				modal.addClass('show');
				modal.html("");
				modal.append('<div class="orGallery_close" onclick="orGallery_close(this);">X</div>');
				modal.append('<img class="orGallery_imgAmpli" src="'+maxi+'" alt="'+altTxt+'" title="'+altTxt+'">');
				if(xmlData)
				{
					modal.append('<div class="orGallery_xmlContent">'+altTxt+'</div>');
					if(current.attr('rel-xmllinktxt') != "")
					{
						var linkTittle = current.attr('rel-xmltitle') == "" ? current.attr('rel-xmllinktxt') : current.attr('rel-xmltitle'); 
						if(current.attr('rel-xmllink') != "")
						{
							modal.append('<div class="orGallery_xmlLink"><a href="'+current.attr('rel-xmllink')+'" title="'+linkTittle+'" target="_blank">'+current.attr('rel-xmllinktxt')+'</a></div>');
						}else
						{
							modal.append('<div class="orGallery_xmlLink">'+current.attr('rel-xmllinktxt')+'</div>');
						}
					}
				}
			}
			
		}		
	});
	jQuery('.orGallery_close').on('click', function(){ orGallery_close(this); });
});
</script>
<?php } ?>
<div class="or_gallery <?php echo $moduleclass_sfx; ?>"  >
	<?php if(!empty($module->content)){ ?>
	<div class="or_blockwidth">
		<?php echo $module->content; ?>
	</div>
	<?php }
	
	if(count($orGallery_fotos) > 0) { ?>
		<div id="orGallery_<?php echo $module->id; ?>" class="or_gallery_content">
			<?php
			foreach($orGallery_fotos as $orGalleryId => $orGalleryFotoData) {
				$orGallery_rel_xml = "";
				if($orGallery_use_xml) {
					$orGallery_rel_xml .= ' rel-xmltitle="';
					$orGallery_rel_xml .= isset($orGalleryFotoData['xml_title']) ? $orGalleryFotoData['xml_title'].'"' : '"';
					$orGallery_rel_xml .= ' rel-xmllink="';
					$orGallery_rel_xml .= isset($orGalleryFotoData['xml_link']) ? $orGalleryFotoData['xml_link'].'"' : '"';
					$orGallery_rel_xml .= ' rel-xmllinktxt="';
					$orGallery_rel_xml .= isset($orGalleryFotoData['xml_link_text']) ? $orGalleryFotoData['xml_link_text'] : '';
					$orGallery_rel_xml .= '"';
				}
				if($orGalleryFotoData['title'] == "")
				{
					$orGalleryFotoData['title'] = $params->get('alt_generico') . ' - '.$orGalleryId;
				}
			?>
			<div class="or_gallery_image" rel-image="<?php echo JURI::root(true).'/'.$orGalleryFotoData['maxi']; ?>" rel-id="<?php echo $orGalleryId; ?>" <?php echo $orGallery_rel_xml; ?>>
				<?php if(!$open_modal) { echo '<a href="'.JURI::root(true).'/'.$orGalleryFotoData['maxi'].' title="'.$orGalleryFotoData['title'].' target="orGalleryPhoto" >'; } ?>
					<img src="<?php echo $orGalleryFotoData['mini']; ?>" loading="lazy" alt="<?php echo $orGalleryFotoData['title']; ?>" title="<?php echo $orGalleryFotoData['title']; ?>" />
					<?php if($orGallery_use_xml && !empty($orGalleryFotoData['xml_title'])) { ?>
						<div class="or_gallery_xml_content">
							<?php if(!empty($orGalleryFotoData['xml_title'])) { ?>
							<div class="or_gallery_xml_title"><?php echo $orGalleryFotoData['xml_title']; ?></div>
							<?php } ?>
						</div>
					<?php }
				if(!$open_modal){ echo '</a>'; } ?>
			</div>
			<?php } ?>
		</div>
		<?php if($open_modal) { ?>
			<div class="or_galleryModal"></div>
		<?php } 
	} ?>
</div>
