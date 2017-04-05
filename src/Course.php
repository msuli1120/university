<?php
  class Course{

    private $id;
    private $course;

    function __construct($course,$id=null){
      $this->course = $course;
      $this->id = $id;
    }

    function getId(){
      return $this->id;
    }

    function setCourse($new_course){
      $this->course = (string) $new_course;
    }

    function getCourse(){
      return $this->course;
    }

    function save(){
      $executed = $GLOBALS['db']->exec("INSERT INTO courses (course) VALUES ('{$this->getCourse()}');");
      if($executed){
        $this->id = $GLOBALS['db']->lastInsertId();
        return true;
      } else {
        return false;
      }
    }

    static function getAll(){
      $new_course_array = array();
      $executed = $GLOBALS['db']->query("SELECT * FROM courses;");
      $results = $executed->fetchAll(PDO::FETCH_ASSOC);
      foreach($results as $result){
        $new_course = new Course($result['course'], $result['id']);
        array_push($new_course_array, $new_course);
      }
      return $new_course_array;
    }

    static function findCourse($id){
      $executed = $GLOBALS['db']->prepare("SELECT * FROM courses WHERE id = :id;");
      $executed->bindParam(':id', $id, PDO::PARAM_INT);
      $executed->execute();
      $result = $executed->fetch(PDO::FETCH_ASSOC);
      $new_course = new Course($result['course'], $result['id']);
      return $new_course;
    }

    function getStudents($course_id){
      $executed = $GLOBALS['db']->prepare("SELECT students.* FROM students JOIN students_courses ON (students_courses.student_id = students.id) JOIN courses ON (courses.id = students_courses.course_id) WHERE course_id = :id;");
      $executed->bindParam(':id', $course_id, PDO::PARAM_INT);
      $executed->execute();
      $results = $executed->fetchAll(PDO::FETCH_ASSOC);
      return $results;
    }


  }
?>
