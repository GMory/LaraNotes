# LaraNotes
[![Travis](https://img.shields.io/travis/GMory/laranotes.svg?style=flat-square)](https://travis-ci.org/GMory/laranotes)
[![Codecov](https://img.shields.io/codecov/c/github/GMory/laranotes/1.1.0.svg?style=flat-square)](https://codecov.io/gh/GMory/laranotes/branch/1.1.0)

A package for laravel that allows you to attach notes to any model of your choosing. This is particularly helpful when you want to add a snippet of information to any of your models.

## Use Cases

Some use cases include: 
- Noting models with readable messages related to exceptions.
- Noting user log-on/off times.
- Noting an article with the last user to edit it.

## Installation

Use Composer to install the package. 

1. You can do this by running:
```
composer require gmory/laranotes
```

2. Add the service provider to your `config/app.php` file:
```
'providers' => [
    ...
    Gmory\Laranotes\LaranotesServiceProvider::class,
];
```

3. Add the Facade to your `config/app.php` file:
```
'Laranote' => Gmory\Laranotes\LaranotesFacade::class,
```

## Usage

### Setup Relationships with NotesTrait
First place the `NotesTrait` on each of the models you wish to attach notes to. This will give those models the appropriate relationships to access their notes.

### Adding Notes
To add a note, specify what model you want the note attached to by using `attach($model)`, followed by the `note($content, [$unique])` method:
```
Laranote::attach($user)->note('This user is great!');
```

You can specify to only add the note if it's unique (ensuring that you don't add a duplicate identical note) by passing true as the second argument in the `note()` method.
```
Laranote::attach($user)->note('If this note already exists, do not attach it again.', true);
```

You can make a note regard a secondary model with `regarding($model)`. This is useful when you want a note attached to a particular model, but you want to know what the note is referencing.
```
Laranote::attach($user)->regarding($post)->note($user->name . ' edited this post.');
```

You can delete all old notes associated with a model when creating a new note with `deleteOld()`.
```
Laranote::attach($user)->deleteOld()->note('I want this to be the only note attached to this user.');
```

### Retrieving Notes
To retrieve notes from a particular model, you can call the `notes()` relationship that the `NotesTrait` granted it.
```
$user->notes
```

To retrieve any notes that are regarding a particular model, you can call the `regardedBy()` relationship that the `NotesTrait` granted it.
```
$user->regardedBy
```

### Note Properties
To return a note's content, call the `content` attribute
```
$note->content
```

To return the model the note is attached to, use the `noting` relationship
```
$note->noting
```

To return the model the note is regarding, use the `regarding` relationship
```
$note->regarding
```

## License
MIT