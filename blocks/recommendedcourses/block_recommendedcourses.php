<?php
class block_recommendedcourses extends block_base {
    public function init() {
        $this->title = get_string('recommendedcourses', 'block_recommendedcourses');
		
    }
	
	public function get_content() {
    if ($this->content !== null) {
      return $this->content;
    }
 
           if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $renderable = new \block_recommendedcourses\output\main(); 
        $renderer = $this->page->get_renderer('block_recommendedcourses');
		
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = ''; 

        return $this->content;
}
}