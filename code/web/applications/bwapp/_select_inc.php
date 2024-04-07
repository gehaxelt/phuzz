<?php
$bugs = file("bugs.txt");
?>
<style>
div#bug a {font-weight: bold; cursor: pointer;}
#bug {bottom: 0; }
#bug form {position: absolute; top: 0; bottom: 0; }
#bug select.stretch {height: 80%; }
#bug div {display: flex; height: 100%; align-items: flex-start;}
#bug button {display: none;}
#bug small {font-size: 70%}
</style>
<div id="bug" class="bugs">

  <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST" name="got_to_bug" target="_blank">

    <label>Choose your bug: <small><input type="checkbox" id="new_tab" checked="checked"> open in new tab</small></label><br />
    <div>
      <a id="prev_bug" title="Go to prev bug">«««</a>
      <select id="bug-select" name="bug" size="3">

        <?php

          // Lists the options from the array 'bugs' (bugs.txt)
          foreach ($bugs as $key => $value)
          {

            $bug = explode(",", trim($value));

            // Debugging
            // echo "key: " . $key;
            // echo " value: " . $bug[0];
            // echo " filename: " . $bug[1] . "<br />";
            $selected = (mb_stristr($bug[1], basename($_SERVER["SCRIPT_NAME"]))!==false)? ' selected="selected"':'';

            echo "
          <option title='$bug[1]' value='$key' $selected>$bug[0]</option>";

          }

        ?>


      </select>
      <a id="next_bug" title="Go to next bug">»»»</a>
    </div>
    <button name="form_bug"></button>

  </form>
<script>
  function adjust_size() {
    var select = document.getElementById('bug-select');
    console.log(window.innerWidth,window.innerHeight,select.getAttribute('size'));
    if(window.innerWidth < 1665) { select.setAttribute('size', 1); select.classList.remove('stretch'); }
    else  { select.setAttribute('size', 3);  select.classList.add('stretch'); }
    console.log(select.getAttribute('size'));
    var same_tab = localStorage.getItem('same_tab');
    if(same_tab) {
      document.querySelector('form[name="got_to_bug"]').setAttribute('target',same_tab);
      if (same_tab == '_self') document.getElementById('new_tab').removeAttribute('checked');
    }
  }
  document.getElementById('prev_bug').addEventListener('click', function (e) {document.getElementById('bug-select').value = parseInt(document.getElementById('bug-select').value)-1;});
  document.getElementById('next_bug').addEventListener('click', function (e) {document.getElementById('bug-select').value = parseInt(document.getElementById('bug-select').value)+1;});
  document.getElementById('bug-select').addEventListener('change', function (e) {document.querySelector('button[name=\'form_bug\']').click();});
  document.getElementById('new_tab').addEventListener('change', function (e) {
    localStorage.setItem('same_tab',e.target.checked?'_blank':'_self');
    document.querySelector('form[name="got_to_bug"]').setAttribute('target',localStorage.getItem('same_tab'));
  });
  window.addEventListener('resize', adjust_size);
  document.addEventListener('DOMContentLoaded', adjust_size);
</script>
</div>
