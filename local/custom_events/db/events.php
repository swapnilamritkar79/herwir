<?php
$observers = array(
    array(
        'eventname'   => '*',
        'callback'    => 'local_custom_events_observer::observe_all',
		'priority' => 99999,
    )
);
