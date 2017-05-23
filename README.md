# LaraNotes
[![Build Status](https://travis-ci.org/GMory/laranotes.svg?branch=master)](https://travis-ci.org/GMory/laranotes)

A package for laravel that allows you to attach notes to any model of your choosing. This is particularly helpful when you want to add a snippet of information to any of your models.

## Use Cases

Some use cases include: 
- Noting models with read-able messages related to exceptions.
- Noting user log-on/off times.
- Noting an article with the last user to edit it.

## Installation

Use Composer to install the package. 

1. You can do this by running:
```
composer require gmory/laranotes
```

2. Once that's complete, you must add the service provider to your `config/app.php` file:
```
'providers' => [
    ...
    Gmory\Laranotes\LaranotesServiceProvider::class,
];
```

3. While not required, you are also free to add the Facade to your `config/app.php` file:
```
'Laranote' => Gmory\Laranotes\LaranotesFacade::class,
```

## Usage

1. First place the `NotesTrait` on each of the models you wish to attach notes to. This will give those models the appropriate relationships to access their notes.

2. Either use the Facade mentioned above or inject Laranote into the class of your choice as a dependency.

3. To add a note, specify what model you want the note attached to by using `attach($model)`, followed by the `note($content)` method:
```
$user = User::first();
Laranote::attach($user)->note('This user is great!');
```

4. Optionally, you may make a note regard a secondary model with `regarding($model)`. This is useful when you want a note attached to a particular model, but you want to know what the note is referencing.
```
$user = User::first();
$post = Post::first();
Laranote::attach($user)->regarding($post)->note($user->name . ' edited this post.');
```

5. Optionally, you may delete all old notes associated with a model when creating a new note with `deleteOld()`.
```
$user = User::first();
Laranote::attach($user)->deleteOld()->note('I want this to be the only note attached to this user.');
```

## License
MIT