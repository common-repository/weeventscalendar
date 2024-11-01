<div id="weecaccordion">
<?php
	$result = '';
            foreach($eventsa as $event)
            {
                $result .= '<h3><a href="#">'.$event['title'].'</a></h3>';
                $result .= '<div>';
                $result .= '<span>';
                $result .= __('Since', 'WEeventscalendar').': ';
                $result .= $event['from_datefield'];
                $result .= '</span><br />';
                $result .= '<span>';
                $result .= __('To', 'WEeventscalendar').': ';
                $result .= $event['to_datefield'];
                $result .= '</span><br />';

                $result .= '<span>';

                $result .= weec_excerpt($event['content']);

                $result .= '</span><br />';

                $result .= '<a href="'.get_permalink($event['id']).'">'.__('View more', 'WEeventscalendar').'</a>';
                $result .= '</div>';
            }
	echo $result;
?>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
jQuery("#weecaccordion").accordion();
});
</script>