<?php
class block_simplehtml extends block_base {
    public function init() {
        $this->title = get_string('simplehtml', 'block_simplehtml');
    }
	
	public function get_content() {
		global $CFG;
    if ($this->content !== null) {
      return $this->content;
    }
 
    $this->content         =  new stdClass;
    $this->content->text   = "<a href=\"$CFG->wwwroot/blocks/simplehtml/course_subscription.php\">".get_string("linkname",'block_simplehtml')."</a> ...";
  //  $this->content->footer = 'Footer here...';
 
    return $this->content;
}
}