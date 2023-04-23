<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];


// Функция принимает ФИО и возвращает склееную строку.

function getFullnameFromParts($surname, $name, $patronymic) {
    return "$surname $name $patronymic";
}

// Функция разюивает методом expldoe массив на субстроки и возвращает 3 новых переменных с ФИО.
// Пустые кавычки- это разделитель, в данном случае- пробел. 

function getPartsFromFullname($fullname) {
    $parts = explode(" ", $fullname);
    return [
        'surname' => $parts[0],
        'name' => $parts[1],
        'patronymic' => $parts[2]
    ];
}

// Здесь создается массив parts, который создается с помощью ранее написаной функции getPartsFromFullname
// mbsubstr достает из parts фамилию и обрезает ее на первом символе, потом добавляет точку.
function getShortName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $shortSurname = mb_substr($parts['surname'], 0, 1) . '.';
    return $parts['name'] . ' ' . $shortSurname;
}

//Пользуясь тем же методом mb_substr проверяем имена на принадлежность к полу, с каждой проверкой меняем переменную $genderSum
// 
function getGenderFromName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $genderSum = 0;
    
    // Признаки женского пола
    if (mb_substr($parts['patronymic'], -3) == 'вна') {
        $genderSum--;
    }
    if (mb_substr($parts['name'], -1) == 'а') {
        $genderSum--;
    }
    if (mb_substr($parts['surname'], -2) == 'ва') {
        $genderSum--;
    }
    
    // Признаки мужского пола
    if (mb_substr($parts['patronymic'], -3) == 'ич') {
        $genderSum++;
    }
    if (mb_substr($parts['name'], -1) == 'й' || mb_substr($parts['name'], -1) == 'н') {
        $genderSum++;
    }
    if (mb_substr($parts['surname'], -1) == 'в') {
        $genderSum++;
    }
    
    if ($genderSum > 0) {
        return 1; // мужской пол
    } elseif ($genderSum < 0) {
        return -1; // женский пол
    } else {
        return 0; // неопределенный пол
    }
}


/* Тут самое сложное это синтаксис вот этих строк
fn($p) => getGenderFromName($p['fullname']) === 'male')); 

Переменная p в данном случае является аргументом функции обратного вызова, которая передается в функцию array_filter() для фильтрации массива $persons.
array_filter() принимает два аргумента: массив для фильтрации и функцию, которая принимает каждый элемент массива и возвращает true или false в зависимости от того, 
нужно ли включить этот элемент в итоговый массив.
Таким образом, каждый элемент массива $persons будет передаваться в функцию обратного вызова и сохраняться в переменной $p. 
Затем вызывается функция getGenderFromName(), которая принимает полное имя $p['fullname'] и возвращает пол, который затем сравнивается со строкой 'female'. 
Если результат равен 'female', то функция fn() возвращает true и элемент массива сохраняется в итоговый массив, который затем будет использоваться для подсчета 
количества женщин в аудитории.*/

function getGenderDescription(array $example_persons_array): string
{
    $total_count = count($example_persons_array);
    $male_count = count(array_filter($example_persons_array, fn($p) => getGenderFromName($p['fullname']) === 'male'));
    $female_count = count(array_filter($example_persons_array, fn($p) => getGenderFromName($p['fullname']) === 'female'));
    $undefined_count = $total_count - $male_count - $female_count;

    $male_percent = round($male_count / $total_count * 100, 1);
    $female_percent = round($female_count / $total_count * 100, 1);
    $undefined_percent = round($undefined_count / $total_count * 100, 1);

    return "Гендерный состав аудитории:\n" .
           "---------------------------\n" .
           "Мужчины - $male_percent%\n" .
           "Женщины - $female_percent%\n" .
           "Не удалось определить - $undefined_percent%\n";
}


function getPerfectPartner(string $surname, string $name, string $patronymic, array $example_persons_array): string
{
    // приводим ФИО к привычному регистру
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronymic = mb_convert_case($patronymic, MB_CASE_TITLE_SIMPLE);

    // склеиваем ФИО
    $fullname = getFullnameFromParts($surname, $name, $patronymic);

    // определяем пол для ФИО
    $gender = getGenderFromName($fullname);

 /* ищем человека противоположного пола
Выбирается случайный человек из массива $example_persons_array с помощью функции array_rand.
С помощью функции getGenderFromName определяется пол выбранного человека.
Если пол выбранного человека совпадает с полом, переданным в переменной $gender, то выполняется новая итерация цикла do-while и повторно выбирается случайный человек из массива.
Если пол выбранного человека не совпадает с полом, переданным в переменной $gender, то цикл do-while завершается и происходит выход из блока кода.
Таким образом, блок кода гарантирует, что случайно выбранный человек из массива $example_persons_array будет противоположного пола, чем пол, переданный в переменной $gender.
*/
    do {
        $random_person = $example_persons_array[array_rand($example_persons_array)];
    } while (getGenderFromName($random_person['fullname']) == $gender);

    // склеиваем ФИО случайного человека
    $random_person_fullname = getFullnameFromParts($random_person['surname'], $random_person['name'], $random_person['patronymic']);


// генерируем случайное число для переменной $compatibility
$compatibility = rand(5000, 10000) / 100; // генерируем число от 50.00 до 100.00
$compatibility = number_format($compatibility, 2); // форматируем до двух знаков после запятой


    // формируем строку с результатом
    $result = "{$fullname} + {$random_person_fullname} = \n♡ Идеально на {$compatibility}% ♡";

    return $result;
}

