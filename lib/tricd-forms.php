<?php

class TRICD_Form {
  
  protected $messages = '';
  
  private $elements;
  
  private $method;
  
  private $id;
  
  protected $additional = '';
  
  protected $args;


  function __construct($id, $args = array()) {
    
    $this->method = 'POST';
    
    $this->id = $id;
    
    $this->args = $args;
    
  }
  
  function isSend() {
    
    return isset($_POST['_cmd_']) && $_POST['_cmd_'] == $this->id;
    
  }
  
  function getId() {
    
    return $this->id;
    
  }
  
  function isValid() {
    
    if (!(isset($_POST['_cmd_']) && $_POST['_cmd_'] == $this->id)) return false;
    
    $valid = true;
    
    foreach($this->elements as $element) {
      
      $valid = $valid && $element->isValid();
      
    }
    
    
    return $valid;
    
  }
  
  function getElementPrefix() {
    
    if(isset($this->args['element_prefix'])) {
      
      return $this->args['element_prefix'];
      
    }
    
    return '<div>';
    
  }
  
  function getElementSuffix() {
    
    if(isset($this->args['element_suffix'])) {
      
      return $this->args['element_suffix'];
      
    }
    
    return '</div>';
    
  }
  
  function getElementWrapperSuffix() {
    
    if(isset($this->args['element_wrapper_suffix'])) {
      
      return $this->args['element_wrapper_suffix'];
      
    }
    
    return '</div>';
    
  }
  
  function getElementWrapperPrefix() {
    
    if(isset($this->args['element_wrapper_prefix'])) {
      
      return $this->args['element_wrapper_prefix'];
      
    }
    
    return '<div>';
    
  }
  
  
  function add($element) {
    
    $element->setForm($this);
    
    $this->elements[$element->id] = $element;
    
  }
  
  
  function getData() {
    
    $data = array();
    
    foreach($this->elements as $element_id => $element) {
      
      if ($element->isValid()) {
        
        $data[$element_id] = $this->getElementData($element_id);
        
      }
      
    }
    
    return $data;
    
  }
  
  function addAdditionalContent($content) {
    
    $this->additional .= $content;
    
  }
  
  
  function getElementData($element_id) {
    
    if (isset($_POST['_cmd_']) && $_POST['_cmd_'] == $this->id) {
      
      if (isset( $_POST[$element_id] )) {
        
        return $_POST[$element_id]; 
      
      }
      
    }
    
    return false;
    
  }
  
  function clear() {
    
    unset($_POST['_cmd_']);
    
  }

  
  function getMessages() {
    
    foreach($this->elements as $element_id => $element) {
    
      $this->messages .= $element->getMessage();
    
    }
    
    return $this->messages;
    
  }
  
  function showSubmit() {
    
    if(isset($this->args['show_submit'])) {
      
      return $this->args['show_submit'];
      
    }
    
    return true;
    
  }
  
  
  function render() {
    
    echo '<form method="'. $this->method .'" id="'. $this->id .'">';
    
    echo $this->getElementWrapperPrefix();
    
    foreach($this->elements as $element) {
      
      echo $this->getElementPrefix();
      echo $element->render();
      echo $this->getElementSuffix();
    }
    
    echo $this->getElementWrapperSuffix();
    
    echo '<br/><input type="hidden" name="_cmd_" value="'. $this->id .'"/>';
    
    if ($this->showSubmit()) {
      echo '<br/><br/><input type="submit" value="submit"/>';
    }
    echo $this->additional;
    
    echo '</form>';
    
  }
  
  
  
  
}



interface TRICD_InterfaceFormElement {
  
  function __construct($id, $label ,$args = array());
  
  function render();
  
  function isValid();
  
  function getData();
  
  function setForm($form);
  
  function getForm();
  
  function validate();
  
}

abstract class TRICD_FormElement implements TRICD_InterfaceFormElement{
  
  public $id;
  protected $args;
  protected $form;
  protected $label;
  
  
  function __construct($id, $label, $args = array()) {
    
    $this->id = $id;
  
    $this->args = $args;

    $this->label = $label;
    
  }
  
  function getClass() {
    
    if(array_key_exists('class', $this->args) && isset($this->args['class'])) {
      
      return ' class="'. $this->args['class'] .'" ';
      
    }
    
    return '';
    
    
  }
  
  
  function isRequired() {
    
    if (array_key_exists('required', $this->args) && isset($this->args['required'])) {
      
      return $this->args['required'];
      
    }
    
    return false;
    
  }
  
  function getMessage() {
    
    if (!$this->isValid()) {  
    
      if (array_key_exists( 'message', $this->args) && isset($this->args['message'])) {

        return $this->args['message'] . '<br/>';

      }

      return 'Required field ' . $this->label . ' missing<br/>';
    
    } else {
      
      return '';
      
    }
    
  }
  
  function render() {
    
    echo 'You need to override the render function';
  
  }
  
  function getData() {
    
    return $this->form->getElementData($this->id);
    
  }
  
  
  function validate() {
    
    if ($this->isRequired() && ($this->getData() === false || $this->getData() == '' )) {
      
      return false;
      
    } else {
      
      return true;
      
    }
    
  }
  
  
  function isValid() {
    
    return $this->validate();
    
  }
  
  function setForm($form) {
    $this->form = $form;
  }
  
  function getForm() {
    $this->form;
  }
  
  
}


class TRICD_TextInput extends TRICD_FormElement{
  
  function render() {
    
    $invalid = '';
    
    if (!$this->isValid()) {
      
      $invalid = ' class="invalid" ';
      
    }
    
    $value = '';
    
    if ($this->getData() !== false) {
      
      $value = $this->getData();
      
    }
    
    return '<input placeholder="'.$this->label.'" '. $this->getClass() .' id="'.$this->id.'" type="text" name="' . $this->id .'" value="'. $value .'"/>';
    
  }
  
}



class TRICD_Select extends TRICD_FormElement{

  function getValues() {
    
    if (isset($this->args['values']) && is_array($this->args['values']) ) {
      
      return $this->args['values'];
      
    } else {
      
      return array();
      
    }
    
  }
  
  
  function render() {
    
    #echo '<br/><br/>';
    echo '<label for="'. $this->id .'">'.$this->label.'</label>';
    echo '<select '. $this->getClass() .' id="'. $this->id .'" name="'. $this->id . '">';
    
    $values = $this->getValues();
    
    foreach($values as $value => $text) {
    
      $selected = '';
      
      if ($this->getData() == $value) $selected = ' selected="selected" ';
      
      echo '<option value="'. $value .'" '. $selected .'>'. $text .'</option>';
      
    }
    
    echo '</select>';
    
  }
  
  
}

class TRICD_RadioButtons extends TRICD_FormElement{
  
  function getValues() {
    
    if (isset($this->args['values']) && is_array($this->args['values']) ) {
      
      return $this->args['values'];
      
    } else {
      
      return array();
      
    }
    
  }
  
  
  
  function render() {
    
    echo '<div style="float:left; width: 100px;">'. $this->label.'</div>';
    
    $values = $this->getValues();
    
    foreach($values as $value => $text) {
      
      $_id = $this->id .'_' . $value;
      
      $selected = '';
      
      if ($this->getData() == $value) $selected = ' checked="checked" ';
      
      echo '<div style="float:left; margin-right:20px;"><input value="'. $value .'" '. $selected .' id="'. $_id .'" type="radio" name="'. $this->id .'"/>
      <label for="'. $_id .'">'. $text .'</label></div>';
    }
    
  }
  
}




class TRICD_TextArea extends TRICD_FormElement{
  
  
  function render() {
    
    $value = '';
    
    if ($this->getData() !== false) {
      
      $value = $this->getData();
      
    }
    
    echo '<label for="'. $this->id .'">'. $this->label .'</label><br/>';
    echo '<textarea class="'. $this->getClass() .'" id="'. $this->id .'" name="'. $this->id .'">'. $value .'</textarea>';
    
  }
  
  
}



?>
