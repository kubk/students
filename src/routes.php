<?php

use App\Type\{StudentType, LogOutType};
use App\{Paginator, LinkGenerator, Student};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\{Request, RedirectResponse};

$app->get('/', function (Request $request) use ($app) {
    $search     = $request->query->get('search', '');
    $pageNumber = $request->query->getInt('page_number', 1);
    $perPage    = $request->query->getInt('per_page', 5);
    $sortBy     = $request->query->get('sort_by', 'id');
    $order      = $request->query->get('order', 'ASC');

    $linkGenerator = new LinkGenerator($search, $order, $perPage, $pageNumber, $sortBy);
    $paginator     = new Paginator($app['studentGateway']->count($search), $perPage, $pageNumber);

    $students = $app['studentGateway']->findAllWith(
        $search, $sortBy, $order, $paginator->getOffset(), $paginator->getPerPage()
    );

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
        $student = $form->getData();
        if (!$isStudentRegistered) {
            $app['authService']->registerStudent($student);
            $app['authService']->rememberStudent($student, $response->headers);
        } else {
            $app['studentGateway']->save($student);
        }
        return $response;
    }

    return $app['twig']->render('form.twig', [
        'form' => $form->createView(),
        'isStudentRegistered' => $isStudentRegistered,
        'search' => '',
    ]);
})->bind('form')->method('GET|POST');

$app->post('/logout', function (Request $request) use ($app) {
    $logOutForm = $app['form.factory']->createBuilder(LogOutType::class)->getForm();
    $logOutForm->handleRequest($request);
    if (!$logOutForm->isValid()) {
        throw new NotFoundHttpException();
    }
    $response = new RedirectResponse($app['url_generator']->generate('student-list'));
    $app['authService']->logOut($response->headers);
    return $response;
})->bind('logout');
