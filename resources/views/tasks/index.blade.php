<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Futuristic To Do App</title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
.pagination nav{
display:flex;
flex-wrap:wrap;
gap:8px;
justify-content:center;
}
.pagination nav div span,
.pagination nav div a{
padding:6px 12px;
border-radius:8px;
background:rgba(255,255,255,0.06);
color:white;
transition:0.2s;
font-size:14px;
}
.pagination nav div a:hover{
background:rgba(255,255,255,0.15);
}
.pagination .active span{
background:#6366f1;
font-weight:600;
}
</style>
</head>

<body 
x-data="{ 
open:false,
taskId:null,
title:'',
description:'',
deadline:'',
priority:'medium'
}" 
class="min-h-screen bg-gradient-to-br from-[#0f0c29] via-[#302b63] to-[#24243e] text-white px-4 sm:px-8 py-8">

@php
$currentFilter = request('filter');
@endphp

<div class="max-w-4xl mx-auto bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-xl p-6 sm:p-10">

<h1 class="text-2xl sm:text-3xl font-semibold text-center mb-8 tracking-wide">
📋 My To Do List
</h1>

{{-- FILTER --}}
<div class="flex flex-wrap justify-center gap-3 mb-8">
<a href="/"
class="px-4 py-2 text-sm rounded-full transition
{{ !$currentFilter ? 'bg-indigo-500 text-white shadow scale-105' : 'bg-white/10 hover:bg-white/20' }}">
Semua
</a>

<a href="/?filter=pending"
class="px-4 py-2 text-sm rounded-full transition
{{ $currentFilter=='pending' ? 'bg-indigo-400 text-white shadow scale-105' : 'bg-white/10 hover:bg-white/20' }}">
Belum
</a>

<a href="/?filter=completed"
class="px-4 py-2 text-sm rounded-full transition
{{ $currentFilter=='completed' ? 'bg-indigo-400 text-white shadow scale-105' : 'bg-white/10 hover:bg-white/20' }}">
Selesai
</a>
</div>

{{-- FORM --}}
<form action="{{ route('tasks.store') }}" method="POST"
class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
@csrf

<input type="text" name="title"
placeholder="Judul Task"
class="bg-white/10 border border-white/15 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-1 focus:ring-indigo-400">

<textarea name="description"
placeholder="Deskripsi Task..."
class="bg-white/10 border border-white/15 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-1 focus:ring-indigo-400"></textarea>

<div>
<label class="text-xs text-indigo-300">Deadline Task</label>
<input type="date" name="deadline"
class="mt-1 w-full bg-white/10 border border-white/15 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-1 focus:ring-indigo-400">
</div>

<select name="priority"
class="bg-[#2a2745] border border-white/20 rounded-xl px-4 py-2 text-white focus:outline-none focus:ring-1 focus:ring-indigo-400 shadow-inner">
<option value="low" class="bg-[#2a2745] text-white">Low</option>
<option value="medium" selected class="bg-[#2a2745] text-white">Medium</option>
<option value="high" class="bg-[#2a2745] text-white">High</option>
</select>

<button class="md:col-span-2 bg-indigo-600 hover:bg-indigo-700 transition rounded-xl px-4 py-2">
Tambah Task
</button>

</form>

{{-- TASK LIST --}}
@foreach($tasks as $task)

@php
$priorityColor = match($task->priority) {
'low' => 'bg-white/5 text-gray-300',
'medium' => 'bg-white/5 text-gray-200',
'high' => 'bg-white/5 text-gray-100',
};
@endphp

<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-4 hover:bg-white/10 transition">

<div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">

{{-- LEFT --}}
<div class="flex-1">

<form action="{{ route('tasks.toggle',$task->id) }}" method="POST">
@csrf
@method('PATCH')
<button class="text-base md:text-lg font-medium text-left {{ $task->is_completed ? 'line-through text-gray-400' : '' }}">
{{ $task->title }}
</button>
</form>

@if($task->description)
<p class="text-sm text-gray-300 mt-2">
{{ $task->description }}
</p>
@endif

<div class="flex flex-wrap gap-2 mt-3 text-xs">

@if($task->deadline)
<span class="bg-white/5 text-blue-300 px-3 py-1 rounded-full">
📅 {{ $task->deadline }}
</span>
@endif

<span class="px-3 py-1 rounded-full {{ $priorityColor }}">
{{ ucfirst($task->priority) }}
</span>

</div>

</div>

{{-- RIGHT ACTIONS --}}
<div class="flex md:flex-col md:items-end gap-3 text-sm shrink-0">

<button 
@click="
open=true;
taskId={{ $task->id }};
title=`{{ $task->title }}`;
description=`{{ $task->description }}`;
deadline='{{ $task->deadline }}';
priority='{{ $task->priority }}';
"
class="text-indigo-300 hover:text-indigo-100 transition">
Edit
</button>

<form action="{{ route('tasks.destroy',$task->id) }}" method="POST">
@csrf
@method('DELETE')
<button class="text-red-400 hover:text-red-200 transition">
Hapus
</button>
</form>

</div>

</div>
</div>

@endforeach

{{-- PAGINATION --}}
<div class="mt-10 pagination">
{{ $tasks->links() }}
</div>

</div>

{{-- MODAL --}}
<div x-show="open"
x-transition.opacity
class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50">

<div @click.away="open=false"
x-transition.scale
class="bg-[#1f1c2c] border border-white/15 rounded-3xl p-8 w-full max-w-md mx-4 shadow-xl">

<h2 class="text-lg font-semibold mb-6 text-center">Edit Task</h2>

<form :action="'/tasks/' + taskId" method="POST" class="space-y-4">
@csrf
@method('PUT')

<input type="text" name="title" x-model="title"
class="w-full bg-white/10 border border-white/15 rounded-xl px-4 py-2 text-white">

<textarea name="description" x-model="description"
class="w-full bg-white/10 border border-white/15 rounded-xl px-4 py-2 text-white"></textarea>

<div>
<label class="text-xs text-indigo-300">Deadline Task</label>
<input type="date" name="deadline" x-model="deadline"
class="mt-1 w-full bg-white/10 border border-white/15 rounded-xl px-4 py-2 text-white">
</div>

<select name="priority" x-model="priority"
class="w-full bg-[#2a2745] border border-white/20 rounded-xl px-4 py-2 text-white shadow-inner">
<option value="low" class="bg-[#2a2745] text-white">Low</option>
<option value="medium" class="bg-[#2a2745] text-white">Medium</option>
<option value="high" class="bg-[#2a2745] text-white">High</option>
</select>

<div class="flex justify-end gap-4 pt-4 text-sm">
<button type="button"
@click="open=false"
class="bg-gray-600 px-4 py-2 rounded-xl hover:bg-gray-500 transition">
Batal
</button>

<button class="bg-indigo-600 px-4 py-2 rounded-xl hover:bg-indigo-700 transition">
Update
</button>
</div>

</form>

</div>
</div>

</body>
</html>
