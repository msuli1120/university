<?php
  date_default_timezone_set('America/Los_Angeles');
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Course.php";
  require_once __DIR__."/../src/Student.php";

  use Symfony\Component\Debug\Debug;
  Debug::enable();

  $app = new Silex\Application();

  $app['debug'] = true;

  $server = 'mysql:host=localhost;dbname=register';
  $user = 'root';
  $pass = 'root';

  $db = new PDO($server, $user, $pass);

  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
  ));

  use Symfony\Component\HttpFoundation\Request;
  Request::enableHttpMethodParameterOverride();

  $app->get("/", function () use ($app) {
    return $app['twig']->render('index.html.twig');
  });

  $app->get("/registercourse", function () use ($app) {
    return $app['twig']->render('registercourse.html.twig', array('courses'=>Course::getAll()));
  });

  $app->post("/addcourse", function () use ($app) {
    if(empty($_POST['course'])){
      return $app['twig']->render('warning.html.twig');
    } else {
      $new_course = new Course($_POST['course']);
      $new_course->save();
      return $app['twig']->render('registercourse.html.twig', array('courses'=>Course::getAll()));
    }
  });

  $app->get("/registerstudent", function () use ($app) {
    return $app['twig']->render('registerstudent.html.twig', array('courses'=>Course::getAll(), 'students'=>Student::getAll(), 'studentscount'=>count(Student::getAll())));
  });

  $app->post("/registerstudent", function () use ($app) {
    if(empty($_POST['name'])||empty($_POST['course_id'])){
      return $app['twig']->render('warning.html.twig');
    } else {
      $new_student = new Student($_POST['name']);
      $new_student->save();
      $course_id_array = $_POST['course_id'];
      foreach($course_id_array as $course_id){
        $new_student->saveCourse($course_id);
      }
      return $app['twig']->render('registerstudent.html.twig', array('courses'=>Course::getAll(), 'students'=>Student::getAll(), 'studentscount'=>count(Student::getAll())));
    }
  });

  $app->get("/student/{id}", function ($id) use ($app) {
    $new_student = Student::find($id);
    $courses = $new_student->getCourses($new_student->getId());
    return $app['twig']->render('student.html.twig', array('courses'=>$courses, 'student'=>$new_student));
  });

  $app->get("/course/{id}", function ($id) use ($app) {
    $new_course = Course::findCourse($id);
    $students = $new_course->getStudents($new_course->getId());
    return $app['twig']->render('course.html.twig', array('students'=>$students, 'course'=>$new_course, 'studentscount'=>count($students)));
  });

  return $app;
?>
