<?php
  error_reporting(E_ALL);
  // @author jmadrigal

  class Github {

    public function __construct(){
       // Grab the method by using $_GET
       $method = $_GET['m'];

       // Check if isset and call the method needed
       if(isset($method)){
         switch($method){
           case "store":
             $this->store();
             break;
         }
       }
    }

    /*
     store() : Method for storing the more popular repositories on github
     */
    public function store() {
      // Grab the post variables being sent from ajax
      $inp = $_POST;
      //var_dump($inp); die();
      // Connect to database
      $db = $this->dbConnect();

      // Loop through the api results and execute the insert/update query
      foreach($inp['items'] as $row) {
        $row =  (object) $row;
        // Create the query string we'll use when storing each object
        $qString =  $db->prepare('INSERT INTO repos (id, name, url, created_at, pushed_at, description, stars)
                        VALUES (:id, :name, :url, :created, :pushed, :descript, :stars)
                        ON DUPLICATE KEY
                        UPDATE name = :name, url = :url, created_at = :created,
                        pushed_at = :pushed, description = :descript, stars = :stars');

        // Bind paramaters to the query. We use binding because mysql automatically
        // sanitizes the inputs given into the database.
        $qString->bindParam(':id', $row->id);
        $qString->bindParam(':name', $row->name, PDO::PARAM_STR);
        $qString->bindParam(':url', $row->url, PDO::PARAM_STR);
        $qString->bindParam(':created', $row->created_at, PDO::PARAM_STR);
        $qString->bindParam(':pushed', $row->pushed_at, PDO::PARAM_STR);
        $qString->bindParam(':descript', $row->description, PDO::PARAM_STR);
        $qString->bindParam(':stars', $row->stargazers_count, PDO::PARAM_STR);

        // Execute query
        $qString->execute();
      }
    }

    /*
     dbConnect() : Connection to the database
     */
    private function dbConnect(){
      $dbname = 'victr';
      $host = 'mysql:host=localhost;dbname='.$dbname;
      $user = 'victr';
      $pass = 'victr';
        try {
          $con = new PDO($host, $user, $pass);
          $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
          echo $e->getMessage();
        }
        return $con;
    }
  }

  $git = new Github;

?>
