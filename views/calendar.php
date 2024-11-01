<script type="text/javascript">
	jQuery(document).ready
	(
		function()
		{
			jQuery("#weecdatepicker").datepicker
			(
				{
					dateFormat: 'yy-mm-dd',
					onSelect: function(dateText, inst)
					{
						jQuery("#datehidden").val(dateText);
						jQuery("#weecdatepickerhform").submit();
		    		}
				}
			);
	    }
	);
</script>
<form method="post" action="" id="weecdatepickerhform">
    <input type="hidden" name="datehidden" id="datehidden" />
	<input type="hidden" name="type" id="type" value="2" />
</form>