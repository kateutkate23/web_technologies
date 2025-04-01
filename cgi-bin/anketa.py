#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import cgi
import sys
import os

# Настройка кодировки вывода
sys.stdout.reconfigure(encoding='utf-8')

form = cgi.FieldStorage()  # Извлечение данных из формы
print("Content-type: text/html; charset=utf-8\n")  # Заголовок с кодировкой UTF-8

# HTML-шаблон для ответа
html = """
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат анкеты</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 50%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin: 20px; font-style: italic; }
    </style>
</head>
<body>
    <h1>Результат анкеты</h1>
    <table>
        <tr><th>Поле</th><th>Значение</th></tr>
"""

# Поля анкеты
fields = ['surname', 'name', 'patronymic', 'level', 'libs', 'ide', 'email', 'source']
labels = ['Фамилия', 'Имя', 'Отчество', 'Уровень знаний', 'Библиотеки', 'IDE', 'Email', 'Источник']
data = []

import urllib.parse

# Обработка данных из формы
for field in fields:
    if field not in form:
        data.append('(не указано)')
    else:
        if not isinstance(form[field], list):
            value = form[field].value
            # Декодируем значение из URL
            decoded_value = urllib.parse.unquote(value, encoding='utf-8')
            data.append(decoded_value)
        else:
            values = [urllib.parse.unquote(x.value, encoding='utf-8') for x in form[field]]
            data.append(', '.join(values))

# Вывод таблицы
for i in range(len(fields)):
    html += f'<tr><td>{labels[i]}</td><td>{data[i]}</td></tr>'

# Расширенное задание 1: добавление строки-шаблона
initials = f"{data[1][0]}.{data[2][0]}." if data[1] and data[2] else "И.О."
summary = f"{initials} {data[0]} является {data[3]}, предпочитает {data[5]} и использует {data[4]}."
html += f"""
    </table>
    <p class="summary">{summary}</p>
</body>
</html>
"""

print(html)

# Расширенное задание 3: запись данных в файл
with open('results.txt', 'a', encoding='utf-8') as f:
    f.write(' | '.join(data) + '\n')