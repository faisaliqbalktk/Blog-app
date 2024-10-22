<?php
include('db.php');

class Tag {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTags() {
        $sql = "SELECT * FROM tags";  // Ensure your 'tags' table has a 'name' column
        $result = mysqli_query($this->db, $sql);
        
        if (!$result) {
            die("Database query failed: " . mysqli_error($this->db));
        }

        return $result;
    }
}
?>
