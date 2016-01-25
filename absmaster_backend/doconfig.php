<?php
  class ProjectState
  {
    private $signup_enabled;
    private $project_state_file = 'project_state.json'; // should set this in constructor!

    private function _parse_project_state_file($file) {
      $json = file_get_contents($file);
      return json_decode($json);
    }

    private function _write_project_state_file($json) {
      $file_cont = json_encode($json);
      file_put_contents($this->project_state_file,$file_cont);
    }

    // this should actually parse the existing config file (using the above function), if it exists, or else create a default one, maybe?
    public function __construct () {
      $state = $this->_parse_project_state_file($this->project_state_file);
      $this->signup_enabled = $state->signup_enabled;
    }

    public function set_signup_enabled(bool $val) {
      $this->signup_enabled = $val;
    }

    public function get_signup_enabled() {
      return $this->signup_enabled;
    }

  }
  
  $foo = ['a'=>35,'b'=>2165,'c'=>'othernumbz'];
  //$foo = [35,2165,'otherfoo'];

  echo print_r($foo)."\n";
  echo json_encode($foo);

  foreach ($foo as $k=>$v) {
    if ($v == 2165) {
      //unset($foo[$k]);
      array_splice($foo,$k,1);
    }
  }

  echo print_r($foo)."\n";
  echo json_encode($foo);

?>
