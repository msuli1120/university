<?php
  class Student {
    private $id;
    private $name;
    private $enroll_date;

    function __construct($name, $enroll_date=null, $id=null){
      $this->name = $name;
      $this->enroll_date = $enroll_date;
      $this->id = $id;
    }

    function getId(){
      return $this->id;
    }

    function getEnrollDate(){
      return $this->enroll_date;
    }

    function setName($new_name){
      $this->name = (string) $new_name;
    }

    function getName(){
      return $this->name;
    }

    function save(){
      $executed = $GLOBALS['db']->query("INSERT INTO students (name, enroll_date) VALUES ('{$this->getName()}', NOW());");
      if($executed){
        $this->id = $GLOBALS['db']->lastInsertId();
        return true;
      } else {
        return false;
      }
    }

    function saveCourse($course_id){
      $executed = $GLOBALS['db']->exec("INSERT INTO students_courses (student_id, course_id) VALUES ({$this->getId()}, $course_id);");
      if($executed){
        return true;
      } else {
        return false;
      }
    }

    static function getAll(){
      $new_student_array = array ();
      $executed = $GLOBALS['db']->query("SELECT * FROM students;");
      $results = $executed->fetchAll(PDO::FETCH_ASSOC);
      foreach($results as $result){
        $new_student = new Student($result['name'], $result['enroll_date'], $result['id']);
        array_push($new_student_array, $new_student);
      }
      return $new_student_array;
    }

    function getCourses($id){
      $executed = $GLOBALS['db']->prepare("SELECT course FROM courses JOIN students_courses ON (students_courses.course_id = courses.id) JOIN students ON (students.id = students_courses.student_id) WHERE students.id = :id;");
      $executed->bindParam(':id', $id, PDO::PARAM_INT);
      $executed->execute();
      $results = $executed->fetchAll(PDO::FETCH_ASSOC);
      return $results;
    }

    static function find($id){
      $executed = $GLOBALS['db']->prepare("SELECT * FROM students WHERE id = :id;");
      $executed->bindParam(':id', $id, PDO::PARAM_INT);
      $executed->execute();
      $result = $executed->fetch(PDO::FETCH_ASSOC);
      $new_student = new Student($result['name'], $result['enroll_date'], $result['id']);
      return $new_student;
    }


  }
?>
