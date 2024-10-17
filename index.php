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
		'#execute': function(){ getimage()},
		'htmlClass': 'K-Res-Button-YesOk',
		'cssText': 'width:auto',
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

Kekule.X.domReady(init);
</script>
</p>
<div style="width: 800px; height: 600px;">
<div id="chemComposer" style="resize: both; width: 100%; height: 600px;" data-widget="Kekule.Editor.Composer"></div>
</div>
<div id="chemViewer" style="display: none;" data-widget="Kekule.ChemWidget.Viewer"></div>


<script>

</script>
<?php
$OUTPUT->footerEnd();
