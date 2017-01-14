<?php

use App\{Paginator, LinkGenerator, Student, StudentType};
use Symfony\Component\HttpFoundation\{Request, RedirectResponse};

$app->get('/', function (Request $request) use ($app) {
    $search = $request->query->get('search', '');
    $pageNumber = $request->query->getInt('page_number', 1);
    $perPage = $request->query->getInt('per_page', 5);
    $sortBy = $request->query->get('sort_by', 'id');
    $order = $request->query->get('order', 'ASC');
    $studentsCount = $app['studentGateway']->count($search);

    $paginator = new Paginator($studentsCount, $perPage, $pageNumber);

    $students = $app['studentGateway']->findAllWith(
        $search,
        $sortBy,
        $order,
        $paginator->getOffset(),
        $paginator->getPerPage()
    );

    $linkGenerator = new LinkGenerator($search, $order, $perPage, $pageNumber, $sortBy);

    return $app['twig']->render('student-list.twig', [
        'students' => $students,
        'isStudentRegistered' => (bool) $app['authService']->getRegisteredStudent($request->cookies),
        'search' => $search,
        'notify' => $request->query->get('notify'),
        'paginator' => $paginator,
        'linkGenerator' => $linkGenerator
    ]);
})->bind('student-list');

$app->match('/form', function (Request $request) use ($app) {
    $student = $app['authService']->getRegisteredStudent($request->cookies) ?: new Student();
    $form    = $app['form.factory']->createBuilder(StudentType::class, $student)->getForm();

    $form->handleRequest($request);
    $isStudentRegistered = $app['authService']->isStudentRegistered($student);
    if ($form->isSubmitted() && $form->isValid()) {
        $notify = ($isStudentRegistered) ? 'Информация обновлена!' : 'Добавлен новый студент!';
        $url = $app['url_generator']->generate('student-list', compact('notify'));
        $response = new RedirectResponse($url);
        if (!$isStudentRegistered) {
            $student = $app['authService']->registerStudent($form->getData());
            $response->headers = $app['authService']->rememberStudent($student, $response->headers);
        } else {
            $app['studentGateway']->save($form->getData());
        }
        return $response;
    }

    return $app['twig']->render('form.twig', [
        'form' => $form->createView(),
        'isStudentRegistered' => $isStudentRegistered,
        'search' => '',
    ]);
})->bind('form')->method('GET|POST');

$app->get('/unregister', function (Request $request) use ($app) {
    $response = new RedirectResponse($app['url_generator']->generate('student-list'));
    $response->headers = $app['authService']->unregister($response->headers);
    return $response;
})->bind('unregister');
