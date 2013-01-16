<html>
	<head>
		<title>Options columns</title>
		<meta charset='UTF-8'>

		<style type="text/css">
			.kiwip_button{
		        padding:3px;
		        -moz-border-radius:6px;
		        -webkit-border-radius:6px;
		        border-radius:6px;  
		        border:1px solid #B7B7B7;
		        background:#EBEBEB;
		        display:inline-block;
		        position:relative;
		        margin-left:2px;
		        text-shadow: 1px 1px 0px #fff;
		        cursor: pointer;
		     }
		     
		     td{
		        padding:4px 0;
		     }
		     
		     td p {
		        font-style: italic;
		        color: #989898;
		        font-size:10px;
		     }

			
			table.layouts td{
				padding-right:20px;
				padding-bottom:20px;
				text-align:center;
			}
			
			table.layouts label{
				font-size:10px;
				color: #979797; 
			}

			table.layouts td img:hover{
				border-color:#71AEC6;
			}
		</style>

	</head>
	<body>
		<div class="content_wrapper">
			<p>Select your columns:</p>
			<table class="layouts">
			   <tr>
			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/2-col.png" onclick="kiwip_send_shortcode('two-cols');" class="kiwip_button" /><br />
					<label>Two Columns</label>
				  </td>

			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/3-col.png" onclick="kiwip_send_shortcode('three-cols');" class="kiwip_button" /><br />
					<label>Three Columns</label>
				  </td>

			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/4-col.png" onclick="kiwip_send_shortcode('four-cols');" class="kiwip_button" /><br />
					<label>Four Columns</label>
				  </td>

			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/5-col.png" onclick="kiwip_send_shortcode('five-cols');" class="kiwip_button" /><br />
					<label>Five Columns</label>
				  </td>       
			   </tr>

			   <tr>
			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/3-1-col.png" onclick="kiwip_send_shortcode('two-three-cols');" class="kiwip_button" /><br />
					<label>2:3 and 1:3 Columns </label>
				  </td>

			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/4-1-col.png" onclick="kiwip_send_shortcode('three-four-cols');" class="kiwip_button" /><br />
					<label>3:4 and 1:4 Columns</label>
				  </td>

			       <td>		
					<img src="<?php echo $_GET['folderUrl']; ?>/images/5-1-col.png" onclick="kiwip_send_shortcode('four-five-cols');" class="kiwip_button" /><br />
					<label>4:5 and 1:5 Columns</label>
				  </td>
			   </tr>
			</table>
		</div>


		<?php
			$dummy_text="Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.";
		?>
		<div id="values" style="display: none;">
			<!-- two cols -->
			<div id="two-cols">
				<div class="clearfix"></div><div class="columns"><div class="column two first"><?php echo $dummy_text;?></div><div class="column two last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>	

			<!-- three cols -->
			<div id="three-cols">
				<div class="clearfix"></div><div class="columns"><div class="column three first"><?php echo $dummy_text;?></div><div class="column three"><?php echo $dummy_text;?></div><div class="column three last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>

			<!-- four cols -->
			<div id="four-cols">
				<div class="clearfix"></div><div class="columns"><div class="column four first"><?php echo $dummy_text;?></div><div class="column four"><?php echo $dummy_text;?></div><div class="column four"><?php echo $dummy_text;?></div><div class="column four last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>

			<!-- five cols -->
			<div id="five-cols">
				<div class="clearfix"></div><div class="columns"><div class="column five first"><?php echo $dummy_text;?></div><div class="column five"><?php echo $dummy_text;?></div><div class="column five"><?php echo $dummy_text;?></div><div class="column five"><?php echo $dummy_text;?></div><div class="column five last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>

			<!-- two-three cols -->
			<div id="two-three-cols">
				<div class="clearfix"></div><div class="columns"><div class="column two-three first"><?php echo $dummy_text;?></div><div class="column three last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>

			<!-- three-three cols -->
			<div id="three-four-cols">
				<div class="clearfix"></div><div class="columns"><div class="column three-four first"><?php echo $dummy_text;?></div><div class="column four last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>
			
			<!-- four-five cols -->
			<div id="four-five-cols">
				<div class="clearfix"></div><div class="columns"><div class="column four-five first"><?php echo $dummy_text;?></div><div class="column five last"><?php echo $dummy_text;?></div></div><div class="clearfix"></div><br />
			</div>
		</div>

		<!-- Javascript -->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>  
		<script type="text/javascript" src="../../js/tiny_mce_popup.js"></script>
		<script type="text/javascript">
			function kiwip_send_shortcode(shortcode) {
			    
				var shortcode_value = jQuery('#'+shortcode).html();

				window.tinyMCE.execInstanceCommand(window.tinyMCE.activeEditor.editorId, 'mceInsertContent', false, shortcode_value);
				window.tinyMCE.activeEditor.execCommand('mceRepaint');
				tinyMCEPopup.close();
			}
		</script>

	</body>
</html>