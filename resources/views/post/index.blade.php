<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Posts</title>
</head>
<body>
	<h1>Posts</h1>
	<ul>
		@foreach ($posts as $post)
			<li><a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a></li>
		@endforeach
	</ul>
</body>
</html>
