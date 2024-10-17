<?php
require_once "../../config.php";

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LTI = LTIX::requireData();
$p = $CFG->dbprefix;
$displayname = $USER->displayname;

$OUTPUT->header();

$OUTPUT->bodyStart(false);
$OUTPUT->flashMessages();



    $OUTPUT->footerStart();
?>


<p>


<script src="../openochem/js/kekule_libs/raphael.js"></script>
<script src="../openochem/js/kekule_libs/raphael.export.js"></script>
<script src="../openochem/js/kekule_libs/Three.js"></script>


<script src="../openochem/js/kekule_libs/dist/kekule.js?modules=widget,chemWidget,spectroscopy,io"></script>

<script src="download.js"></script>

<link rel="stylesheet" type="text/css" href="../openochem/js/kekule_libs/dist/themes/default/kekule.css" />



<script>

function getimage () {

     var molecule = chemComposer.getChemObj();
     structures = Kekule.IO.saveFormatData(molecule, 'Kekule-JSON');
     chemViewer = Kekule.Widget.getWidgetById('chemViewer');
     chemViewer.setAutofit(true);
     chemViewer.setChemObj(molecule);
     dataUri = chemViewer.exportToDataUri();
     var seconds = new Date() / 1000;
     download(dataUri, seconds+".png", "image/png");
}




var chemEditor;
var chemComposer;
function init()
{

	var elem = document.getElementById('chemComposer');
	var chemEditor = new Kekule.Editor.ChemSpaceEditor(document, null, Kekule.Render.RendererType.R2D);
	chemComposer = new Kekule.Editor.Composer(elem, chemEditor);
	chemComposer.renderConfigs.getColorConfigs().setUseAtomSpecifiedColor(true);
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefChargeMarkType(3);
        chemComposer.getEditorConfigs().getInteractionConfigs().setAllowUnknownAtomSymbol(false);
        

      chemComposer
        .setEnableDimensionTransform(true)
        .setAutoSetMinDimension(true)
        //.setAutoResizeConstraints({width: 1, height: 1})
        //.autoResizeToClient();  // force a resize to window client




        var N = Kekule.ChemWidget.ComponentWidgetNames;
var C = Kekule.Editor.ObjModifier.Category;

// Common toolbar buttons
chemComposer.setCommonToolButtons([
	N.newDoc,
	N.loadData,
	N.saveData,
	N.undo,
	N.redo,
	N.copy,
	N.cut,
	N.paste,
	N.zoomIn,
	N.zoomOut,
	N.config,
	//N.objInspector
	{
		"name": "commonCustom1",
		"text": "Get Image",
		'showText': true,
		// use your own Action class here to do some concrete work
		"actionClass": Class.create(Kekule.Editor.ActionOnComposerAdv, {}),
		"hint": "Custom action 1",
//		'#execute': function(){ getimage()},
		'#execute': function(){ exportCroppedImage()},
		'htmlClass': 'K-Res-Button-YesOk',
		'cssText': 'width:auto',
	},
	{
	'text': 'IUPAC Names',  // button caption
	'htmlClass': 'K-Res-Button-YesOk',  // show a OK icon
	'hint': 'Copy IUPAC names to clipboard.',
	'showText': true,   // display caption of button
	'#execute': function(){ parseIupacName(); }  // event handler when executing the button
	}
	
	
]);

// Chem toolbar buttons
chemComposer.setChemToolButtons([
	{
		"name": N.manipulate,
		"attached": [
			N.manipulateMarquee,
			N.manipulateLasso,
			N.manipulateBrush,
			N.manipulateAncestor,
			N.dragScroll,
			N.toggleSelect
		]
	},
	N.erase,
	{
		"name": N.molBond,
		"attached": [
			N.molBondSingle,
			N.molBondDouble,
			N.molBondTriple,
			N.molBondWedgeUp,
			N.molBondWedgeDown,
			N.molChain,
			N.trackInput,
			N.molRepFischer1,
			N.molRepSawhorseStaggered,
			N.molRepSawhorseEclipsed
		]
	},
	{
		"name": N.molAtomAndFormula,
		"attached": [
			N.molAtom,
			N.molFormula
		]
	},
	N.molRepMethane,
	{
		"name": N.molRing,
		"attached": [
			N.molRing3,
			N.molRing4,
			N.molRing5,
			N.molRing6,
			N.molFlexRing,
			N.molRingAr6,
			N.molRepCyclopentaneHaworth1,
			N.molRepCyclohexaneHaworth1,
			N.molRepCyclohexaneChair1,
			N.molRepCyclohexaneChair2
		]
	},
	{
		"name": N.molCharge,
		"attached": [
			N.molChargeClear,
			N.molChargePositive,
			N.molChargeNegative,
			N.molRadicalSinglet,
			N.molRadicalTriplet,
			N.molRadicalDoublet,
			N.molElectronLonePair
		]
	},
	{
		"name": N.glyph,
		"attached": [
			N.glyphReactionArrowNormal,
			N.glyphReactionArrowReversible,
			N.glyphReactionArrowResonance,
			N.glyphReactionArrowRetrosynthesis,
			N.glyphRepSegment,
			N.glyphElectronPushingArrowDouble,
			N.glyphElectronPushingArrowSingle,
			N.glyphRepHeatSymbol,
			N.glyphRepAddSymbol
		]
	},
	{
		"name": N.textImage,
		"attached": [
			N.textBlock,
			N.imageBlock
		]
	}
]);

// Object modifiers
chemComposer.setAllowedObjModifierCategories([C.GENERAL, C.CHEM_STRUCTURE, C.GLYPH, C.STYLE, C.MISC]);
        
	// adjust size
	//adjustSize();

	//window.onresize = adjustSize;


Kekule.Editor.IaControllerManager.register(Kekule.Editor.MolSpecifiedAtomIaController, Kekule.Editor.BaseEditor);

// create related actions
Kekule.Editor.ActionComposerMolSpecifiedAtomController_C = Kekule.Editor.createComposerIaControllerActionClass(
	'Kekule.Editor.ActionComposerMolSpecifiedAtomController_C',
	'C', //caption
	'Carbon atom', //hint
	'MolSpecifiedAtomIaController',   // the controller name
	'MolSpecifiedAtomIaController-C',  // html class name for the button
	{ 'specifiedAtomSymbol': 'C' }  // initial property values of controller
);
Kekule.Editor.ActionComposerMolSpecifiedAtomController_O = Kekule.Editor.createComposerIaControllerActionClass(
	'Kekule.Editor.ActionComposerMolSpecifiedAtomController_O',
	'O',
	'Oxygen atom',
	'MolSpecifiedAtomIaController',
	'MolSpecifiedAtomIaController-O',
	{ 'specifiedAtomSymbol': 'O' }
);
Kekule.Editor.ActionComposerMolSpecifiedAtomController_N = Kekule.Editor.createComposerIaControllerActionClass(
	'Kekule.Editor.ActionComposerMolSpecifiedAtomController_N',
	'O',
	'Oxygen atom',
	'MolSpecifiedAtomIaController',
	'MolSpecifiedAtomIaController-N',
	{ 'specifiedAtomSymbol': 'N' }
);
Kekule.Editor.ActionComposerMolSpecifiedAtomController_H = Kekule.Editor.createComposerIaControllerActionClass(
	'Kekule.Editor.ActionComposerMolSpecifiedAtomController_H',
	'H',
	'Hydrogen atom',
	'MolSpecifiedAtomIaController',
	'MolSpecifiedAtomIaController-H',
	{ 'specifiedAtomSymbol': 'H' }
);



	
Kekule.ActionManager.registerNamedActionClass('specifiedAtomH', Kekule.Editor.ActionComposerMolSpecifiedAtomController_H, Kekule.Editor.ChemSpaceEditor);
Kekule.ActionManager.registerNamedActionClass('specifiedAtomC', Kekule.Editor.ActionComposerMolSpecifiedAtomController_C, Kekule.Editor.ChemSpaceEditor);
Kekule.ActionManager.registerNamedActionClass('specifiedAtomO', Kekule.Editor.ActionComposerMolSpecifiedAtomController_O, Kekule.Editor.ChemSpaceEditor);
Kekule.ActionManager.registerNamedActionClass('specifiedAtomN', Kekule.Editor.ActionComposerMolSpecifiedAtomController_N, Kekule.Editor.ChemSpaceEditor);
}



/*
function downloadCroppedImage() {
    //const canvas = document.getElementById('projectionCanvas');
    const canvas = document.querySelector('canvas')
    const ctx = canvas.getContext('2d');
    
    // Define the cropping region
    const cropX = 0; // X-coordinate of the cropping rectangle
    const cropY = 0; // Y-coordinate of the cropping rectangle
    const cropWidth = 300; // Width of the cropping rectangle
    const cropHeight = 300; // Height of the cropping rectangle

    // Create a temporary canvas to hold the cropped image
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = cropWidth;
    tempCanvas.height = cropHeight;
    const tempCtx = tempCanvas.getContext('2d');

    // Draw the cropped region onto the temporary canvas
    tempCtx.drawImage(canvas, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight);

    // Convert the temporary canvas to an image
    const dataURL = tempCanvas.toDataURL('image/png');

    // Create a link element and trigger download
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'newman_projection.png';
    link.click();
}
*/


function exportCroppedImage() {
    //const canvas = document.getElementById('projectionCanvas');
    const canvas = document.querySelector('canvas');
    const ctx = canvas.getContext('2d');
    
    let image = cropImageFromCanvas(ctx);
    var seconds = new Date() / 1000;
    download(image, seconds+".png", "image/png");
    
    //let imghtml = "<img "+"src='"+image+"'</img>";
     
     
    //parent.tinymce.activeEditor.selection.setContent(imghtml);
    //parent.tinymce.activeEditor.windowManager.close();
    
}



function cropImageFromCanvas(ctx) {
  var canvas = ctx.canvas, 
    w = canvas.width, h = canvas.height,
    pix = {x:[], y:[]},
    imageData = ctx.getImageData(0,0,canvas.width,canvas.height),
    x, y, index;
    
    
  // Create a temporary canvas to hold the cropped image
  const tempCanvas = document.createElement('canvas');
  
  for (y = 0; y < h; y++) {
    for (x = 0; x < w; x++) {
      index = (y * w + x) * 4;
      if (imageData.data[index+3] > 0) {
        pix.x.push(x);
        pix.y.push(y);
      } 
    }
  }
  pix.x.sort(function(a,b){return a-b});
  pix.y.sort(function(a,b){return a-b});
  var n = pix.x.length-1;
  
  w = 1 + pix.x[n] - pix.x[0];
  h = 1 + pix.y[n] - pix.y[0];
  
  tempCanvas.width = w+5;
  tempCanvas.height = h+5;
  const tempCtx = tempCanvas.getContext('2d');
  tempCtx.drawImage(canvas, pix.x[0]-5, pix.y[0]-5, w+5, h+5, 0, 0, w, h);
  
  return tempCanvas.toDataURL('image/png');      
}





function adjustSize()
{
	//window.onresize = null;
	//var dim = Kekule.HtmlElementUtils.getViewportDimension(document);
	console.log("RESIZE");
	var margin = {width: 10, height: 10}; //{'width': 50, 'height': 30};
	var dim = Kekule.DocumentUtils.getClientDimension(document);
	console.log(dim);
	dim.width -= margin.width;
	dim.height -= margin.height;
	//chemComposer.setWidth(dim.width - 200 + 'px').setHeight(dim.height - 100 + 'px');
	chemComposer.setWidth(dim.width - 400 + 'px').setHeight(dim.height - 300 + 'px');
	console.log('set composer: ', dim.width, dim.height);
	//window.onresize = adjustSize;
}

function copyToClipboard(text) {
    var dummy = document.createElement("textarea");
    // to avoid breaking orgain page when copying more words
    // cant copy when adding below this code
    // dummy.style.display = 'none'
    document.body.appendChild(dummy);
    //Be careful if you use texarea. setAttribute('value', value), which works with "input" does not work with "textarea". – Eduard
    dummy.value = text;
    dummy.select();
    document.execCommand("copy");
    document.body.removeChild(dummy);
}


function parseIupacName() {
    
    
		//var mols = getComposer().exportObjs(Kekule.Molecule);
		var mols = chemComposer.getChemObj();

		
		var smi = Kekule.IO.saveFormatData(mols, 'smi');

				$.ajax({
				        type: "POST",
  					url: "parsename.php",
  					data:{smiles: smi},
  					success: function(response) { 
                                          //console.log(response);
                                           //document.execCommand("copy");
                                           //parent.tinymce.activeEditor.execCommand("copy");
       
                                           copyToClipboard(response);
                                           
                                           alert("Copied to clipboard: \n" + response);cropI
                                           
                                           
                                       }
  					
  					});
    
    
}


function handleCheckboxChange(checkbox) {
    if (checkbox.checked) {
        // Code to run when the checkbox is checked
        console.log("Checkbox is checked, running first script");
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefMoleculeDisplayType(2);
        // Add your JavaScript logic here for when the checkbox is checked
    } else {
        // Code to run when the checkbox is unchecked
        console.log("Checkbox is unchecked, running second script");
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefMoleculeDisplayType(1);
        // Add your JavaScript logic here for when the checkbox is unchecked
    }
}





Kekule.X.domReady(init);
</script>
</p>
Show implicit H's<input type="checkbox" id="showimplicitH" name="showimplicitH" onchange="handleCheckboxChange(this)">
<div style="width: 800px; height: 600px;">
<div id="chemComposer" style="resize: both; width: 100%; height: 600px;" data-widget="Kekule.Editor.Composer"></div>
</div>
<div id="chemViewer" style="display: none;" data-widget="Kekule.ChemWidget.Viewer"></div>


<script>

</script>
<?php
$OUTPUT->footerEnd();
