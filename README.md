# TRICD FORMS

TRICD Forms is developed to do easy form building, presenting and server side form validation.

## Build a Form

The easiest way to create a standard form is like that:

    $form = new TRICD_Form('add_address');
    
After you created that form, you can add several standard input field to it:

    $input_text = new TRICD_Textinput('forename', 'Forename', array('required' => true));
    
    $form->add($input_text);
    
The show these form on a website, you can use the form render function:

    $form->render();
    
This will output the form - of course with values when already send.

The check if all required field are set, you can just call the isValid function:

    if($form->isValid()) …
    
Of course you can also know, when is the form is send:

    if($form->isSend())
    
The build like a add to addressbook form, you can then use:

    if($form->isSend() && $form->isValid()) {
    
      // add data to addressbook
      add_to_addressbook($form->getData());
      
      // remove standard values
      $form->clear();
    
    } else {
    
      echo $form->getMessages();
    
    }
    
    $form->render();
    
## Input Elements

TRICD_Forms comes with a bunch of predefined Elements.

### TRICD_TextInput
### TRICD_Text
### TRICD_Select
### TRICD_Checkbox
### TRICD_Checkboxes
### TRICD_RadioButtons