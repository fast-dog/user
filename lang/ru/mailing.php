<?php
return [
    'name' => 'Название',
    'date_registration' => 'Дата регистрации',
    'domain' => 'Домен',
    'status' => 'Состояние',
    'state' => [
        'default' => 'По умолчанию',
        'ready' => 'Ожидание выполнения',
        'work' => 'В работе',
        'error' => 'Прервана с ошибкой',
        'finish' => 'Успешно завершена',
    ],
    'status_list' => [
        'ready' => 'Готово к отправке',
        'process' => 'Выполняется отправка',
        'finish' => 'Отправка завершена',
    ],
    'template' => [
        'properties' => [
            'form_address' => 'Email отправителя',
            'form_address_description' => 'Email адрес отправителья письма',
            'from_name' => 'Отправитель письма',
            'from_name_description' => 'Отправитель письма'
        ],
        'new' => [
            'name' => 'Мой первый шаблон массовой рассылки',
            'html' => '<h1>Hello World</h1>'
        ]
    ]
];
