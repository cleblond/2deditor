


function handleDisplayRadioButtonChange(value) {
    if (value === 'skeletal') {
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefMoleculeDisplayType(1);
    } else if (value === 'condensed') {
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefMoleculeDisplayType(2);
    }
    
    chemComposer.repaint();
}


function handleHRadioButtonChange(value) {
    if (value === 'showH') {
        // JavaScript for showing all hydrogens
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefHydrogenDisplayLevel(Kekule.Render.HydrogenDisplayLevel.ALL);
    } else if (value === 'smart') {
        // JavaScript for smart H behavior
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefHydrogenDisplayLevel(Kekule.Render.HydrogenDisplayLevel.LABELED);
    } else if (value === 'off') {
        // JavaScript for turning off hydrogens
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefHydrogenDisplayLevel(Kekule.Render.HydrogenDisplayLevel.NONE);
    }
    
    chemComposer.repaint();
}

/*
	function report(stateText)
	{
		document.getElementById('state').innerHTML = stateText;
	}
*/
	function getCurrMol()
	{
		//return composer.getChemSpace().getChildAt(0);
		return Kekule.ChemStructureUtils.getTotalStructFragment(chemComposer.getChemObj());
	}
	
	function calcStart()
	{
		btnGen.setEnabled(false);
		btnTerminate.setEnabled(true);
		timeStart = Date.now();
	}
	function calcEnd()
	{
		btnGen.setEnabled(true);
		btnTerminate.setEnabled(false);
		timeEnd = Date.now();
	}
		
	function generate3D()
	{
		var mol = getCurrMol();
		console.log('Calculating...');
		calcStart();
		calculator = Kekule.Calculator.generate3D(mol, {'forceField': ''},
			function(generatedMol){
				calcEnd();
				var elapse = (timeEnd - timeStart) / 1000;
				console.log(generatedMol);
				
				console.log(chemViewer);
                $("#3Ddiv").show();
				chemViewer3D.setChemObj(generatedMol);
				console.log('Calculation done in ' + elapse + ' sec');
			},
			function(err)
			{
				if (err)
				{
					calcEnd();
					console.log(err.getMessage? err.getMessage(): err);
					Kekule.error(err);					
				}
			}
		);
	}
	function terminate()
	{
		report('Terminated by user');
		calcEnd();
		if (calculator)
		{
			calculator.halt();			
		}		
	}




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
var chemComposer, chemViewer3D;

function init()
{


// Object modifiers
Kekule.Editor.MolSpecifiedAtomIaController = Class.create(Kekule.Editor.BaseEditorIaController,
/** @lends Kekule.Editor.MolSpecifiedAtomIaController# */
{
	/** @private */
	CLASS_NAME: 'Kekule.Editor.MolSpecifiedAtomIaController',
	/** @private */
	initProperties: function()
	{
	
		this.defineProp('specifiedAtomSymbol', {'dataType': DataType.STRING});
	},
	/** @private */
	canInteractWithObj: function(obj)
	{
		if (this.isValidNode(obj))
			return true;
		else
			return false;
	},

	isValidNode: function(obj)
	{
	
		return (obj instanceof Kekule.ChemStructureNode) && !(obj instanceof Kekule.StructureFragment && obj.isStandalone());
	},

	/** @private */
	changeNode: function(node)
	{
		var newNodeClass = Kekule.Atom;
		var modifiedProps = {
			'isotopeId': this.getSpecifiedAtomSymbol(),
			'explicitHydrogenCount': null
		};
		this.applyModification(node, null, newNodeClass, modifiedProps);
	},

	applyModification: function(node, newNode, newNodeClass, modifiedProps)
	{
		var operation = Kekule.Editor.OperationUtils.createNodeModificationOperation(node, newNode, newNodeClass, modifiedProps, this.getEditor());

		if (operation)  // only execute when there is real modification
		{
			var editor = this.getEditor();
			editor.beginManipulateAndUpdateObject();
			try
			{
				editor.execOperation(operation);
			}
			catch (e)
			{
				throw(e);
			}
			finally
			{
				editor.endManipulateAndUpdateObject();
			}
		}
	},

	/** @private */
	react_pointerup: function(e)
	{
		if (e.getButton() === Kekule.X.Event.MOUSE_BTN_LEFT)
		{
			this.getEditor().setSelection(null);
			var coord = this._getEventMouseCoord(e);
			{
				var obj = this.getTopmostInteractableObjAtScreenCoord(coord);
				if (obj && this.isValidNode(obj))  // can modify atom of this object
				{
					this.changeNode(obj);
					e.preventDefault();
					e.stopPropagation();
				}
				return true;
			}
		}
	}
});


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





	var elem = document.getElementById('chemComposer');
	var chemEditor = new Kekule.Editor.ChemSpaceEditor(document, null, Kekule.Render.RendererType.R2D);
	chemComposer = new Kekule.Editor.Composer(elem, chemEditor);
	chemViewer3D = Kekule.Widget.getWidgetById('chemViewer3D');

	
	
	chemComposer.renderConfigs.getColorConfigs().setUseAtomSpecifiedColor(true);
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefChargeMarkType(3);
        chemComposer.getEditorConfigs().getInteractionConfigs().setAllowUnknownAtomSymbol(false);
        

      chemComposer
        .setEnableDimensionTransform(true)
        .setAutoSetMinDimension(true);

    chemComposer.renderConfigs.getColorConfigs().setUseAtomSpecifiedColor(true);
    chemComposer.getEditorConfigs().getInteractionConfigs().setAllowUnknownAtomSymbol(false);
    chemComposer.getEditor().getIaController('MolBondIaController').setAutoSwitchBondOrder(true);       

    var N = Kekule.ChemWidget.ComponentWidgetNames;
    
    console.log(N);
    
    
    var C = Kekule.Editor.ObjModifier.Category;

	chemComposer.setAllowedObjModifierCategories([C.GENERAL, C.CHEM_STRUCTURE, C.GLYPH, C.STYLE, C.MISC]);


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
		"actionClass": Class.create(Kekule.Editor.ActionOnComposerAdv, {}),
		"hint": "Download Image",
		'#execute': function(){ exportCroppedImage()},
		'htmlClass': 'K-Chem-ImageBlockIaController',
		'cssText': 'width:auto',
	},
	{
	'text': 'IUPAC',  // button caption
	'hint': 'Copy IUPAC names to clipboard.',
	'showGlyph': false,
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
			//N.molBondAromatic,
			N.molBondDouble,
			N.molBondTriple,
			N.molBondWedgeUp,
			N.molBondWedgeDown,
			N.molChain,
			N.trackInput,
			N.molRepFischer1,
			N.molRepFischer3,
			N.molRepSawhorseStaggered,
			N.molRepSawhorseEclipsed,
			N.molBondWedgeUpOrDown
		]
	},
	
	
	{'name': 'Custom', 'actionClass': Kekule.Editor.ActionOnComposerAdv,
                'text': 'Create', 'hint': 'Add atoms and bonds', 'id': 'btnMyCreate', 'htmlClass': 'MYBUTTON',
                'widget': Kekule.Widget.RadioButton,
                'attached': [
                N.molRepMethane, N.molBondSingle, N.molBondDouble, N.molBondTriple, N.molBondWedgeUp, N.molBondWedgeDown, N.molChain,
                N.molAtom, 'specifiedAtomH', 'specifiedAtomN', 'specifiedAtomO'
    ]},
	{
		"name": N.molAtomAndFormula,
		"attached": [
			N.molAtom,
			N.molFormula
		]
	},
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
	N.molCharge,
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



var toolButtons = chemComposer.getCommonToolButtons() || chemComposer.getDefaultCommonToolBarButtons();
		toolButtons.push({
			'id': 'btnGen',
			'text': '3D', 'hint': 'Generate 3D structure', 'showText': true, 'showGlyph': false, 'cssText': 'width:auto',
      '#execute': generate3D
    });
		toolButtons.push({
			'id': 'btnTerminate',
			'text': 'Terminate', 'hint': 'Terminate calculation', 'showText': true, 'showGlyph': false, 'cssText': 'width:auto',
      '#execute': terminate
    });
		chemComposer.setCommonToolButtons(toolButtons);
		btnGen = Kekule.Widget.getWidgetById('btnGen');
		btnTerminate = Kekule.Widget.getWidgetById('btnTerminate');
		btnTerminate.setEnabled(false);


}


function exportCroppedImageNew() {
// create a hidden autosized viewer widget
let viewer = new Kekule.ChemWidget.Viewer(document);
viewer.appendToElem(document.body);
viewer.getElement().style.visibility = 'hidden';
viewer.setAutoSize(true);
// load objects in viewer
viewer.setChemObj(chemComposer.getChemObj());
// export image
let dataUri = viewer.exportToDataUri();

const fileName = prompt("Please enter the file name", "my-image.png");
    
    
    
    if (!fileName) {
        alert("No filename provided, download cancelled.");
            return;
    } else {
        download(dataUri, fileName + ".png", "image/png");
    }


}



function exportCroppedImage() {
    //const canvas = document.getElementById('projectionCanvas');
    const canvas = document.querySelector('canvas');
    const ctx = canvas.getContext('2d');
    
    let image = cropImageFromCanvas(ctx);
    var seconds = new Date() / 1000;
    
    
    const fileName = prompt("Please enter the file name", seconds+".png");
    
    
    
    if (!fileName) {
        alert("No filename provided, download cancelled.");
            return;
    } else {
        download(image, fileName + ".png", "image/png");
    }
    
    
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
	console.log("RESIZE");
	var margin = {width: 10, height: 10}; //{'width': 50, 'height': 30};
	var dim = Kekule.DocumentUtils.getClientDimension(document);
	console.log(dim);
	dim.width -= margin.width;
	dim.height -= margin.height;
	//chemComposer.setWidth(dim.width - 200 + 'px').setHeight(dim.height - 100 + 'px');
	chemComposer.setWidth(dim.width - 400 + 'px').setHeight(dim.height - 300 + 'px');
	console.log('set composer: ', dim.width, dim.height);
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
       
                                           copyToClipboard(response);
                                           
                                           alert("Copied to clipboard: \n" + response);
                                           
                                       }
  					
  					});
    
    
}


function handleCheckboxChange(checkbox) {
    if (checkbox.checked) {
        // Code to run when the checkbox is checked
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefMoleculeDisplayType(2);
        // Add your JavaScript logic here for when the checkbox is checked
    } else {
        // Code to run when the checkbox is unchecked
        chemComposer.renderConfigs.getMoleculeDisplayConfigs().setDefMoleculeDisplayType(1);
        // Add your JavaScript logic here for when the checkbox is unchecked
    }
}





Kekule.X.domReady(init);
