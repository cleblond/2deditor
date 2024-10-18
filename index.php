<?php
require_once "../../config.php";

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LTI = LTIX::requireData();
$p = $CFG->dbprefix;
$displayname = $USER->displayname;

$OUTPUT->header();

$OUTPUT->bodyStart();
$OUTPUT->flashMessages();



    $OUTPUT->footerStart();
?>


<style>
    .MYBUTTON > .K-Pri-Glyph-Content
    {
      background-image: url(images/create.png);
    }
    
    
    /** custom IA controller and action styles **/
.K-Chem-Composer-Assoc-Toolbar .K-Chem-MolSpecifiedAtomIaController-C > .K-Pri-Glyph-Content
{
	background-image: url("./icons/atomC.png");
}
.K-Chem-Composer-Assoc-Toolbar .K-Chem-MolSpecifiedAtomIaController-O > .K-Pri-Glyph-Content
{
	background-image: url("./icons/atomO.png");
}
.K-Chem-Composer-Assoc-Toolbar .K-Chem-MolSpecifiedAtomIaController-N > .K-Pri-Glyph-Content
{
	background-image: url("./icons/atomN.png");
}

.K-Chem-Composer-Assoc-Toolbar .K-Chem-MolSpecifiedAtomIaController-H > .K-Pri-Glyph-Content
{
	background-image: url("./icons/atomH.png");
}


/* Change the default atom tool icon, avoiding conflict with atomC.png */
.K-Chem-Composer-Assoc-Toolbar .K-Chem-MolAtomIaController > .K-Pri-Glyph-Content
{
	background-image: url("./icons/atomC.png");
}
    

</style>



<p>

<script src="../openochem/js/kekule_libs/Three.js"></script>
<script src="../openochem/js/kekule_libs/raphael-min.2.0.1.js"></script>

<!--
<script src="../openochem/js/kekule_libs/raphael.js"></script>
<script src="../openochem/js/kekule_libs/raphael.export.js"></script>
<script src="../openochem/js/kekule_libs/Three.js"></script> -->


<script src="../openochem/js/kekule_libs/dist/kekule.js?modules=widget,chemWidget,calculation,spectroscopy,io,openbabel"></script>

<script src="download.js"></script>

<link rel="stylesheet" type="text/css" href="../openochem/js/kekule_libs/dist/themes/default/kekule.css" />

<script src = "js/app.js"></script>

<script>

</script>


<div class="container-fluid">
    <div class="row">

        <div class="col-md-8">
        

            <div id="chemComposer" style="resize: both; width: 100%; height: 600px;" data-widget="Kekule.Editor.Composer"></div>

            <div id="chemViewer" style="display: none;" data-widget="Kekule.ChemWidget.Viewer"></div>
        
        </div>
        <div class="col-md-4">
        
            <h4>Display Mode</h4>
            <label>
                <input type="radio" name="displayOption" value="skeletal" onclick="handleDisplayRadioButtonChange(this.value)" checked> Skeletal
            </label>
            <label>
                <input type="radio" name="displayOption" value="condensed" onclick="handleDisplayRadioButtonChange(this.value)"> Condensed
            </label>
        <br>


            <h4>Hydrogen Display Mode</h4>
            <label>
                <input type="radio" name="hydrogenOption" value="smart" onclick="handleHRadioButtonChange(this.value)" checked> Smart
            </label>

            <label>
                <input type="radio" name="hydrogenOption" value="showH" onclick="handleHRadioButtonChange(this.value)"> All
            </label>

            <label>
                <input type="radio" name="hydrogenOption" value="off" onclick="handleHRadioButtonChange(this.value)"> Off
            </label>
        
        
            <div style="display: none; width: 100%" id="3Ddiv">
            <h4>3D Structure</h4>
            <div id="chemViewer3D" data-widget="Kekule.ChemWidget.Viewer3D" data-predefined-setting="fullFunc" data-toolbar-evoke-modes="[1]" data-toolbar-pos="1" data-toolbar-margin-horizontal="0" data-toolbar-margin-vertical="0" data-resizable="true"></div>
            </div>
        
        </div>
        
        

        
        






    </div>
</div>



<script>

</script>
<?php
$OUTPUT->footerEnd();
