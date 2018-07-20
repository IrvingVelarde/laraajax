<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Exportar PDF</title>
</head>
<body>
	<table border="1">
		<thead>
			<tr >
				<th>Id</th>
				<th>Nombre</th>
				<th>Email</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($contacts as $contact)
				<tr>
					<td>{{ $contact->id }}</td>
					<td>{{ $contact->name }}</td>
					<td>{{ $contact->email }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>