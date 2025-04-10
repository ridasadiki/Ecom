@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 border-b text-left">Name</th>
                    <th class="px-6 py-3 border-b text-left">Products Count</th>
                    <th class="px-6 py-3 border-b text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 border-b">{{ $category->name }}</td>
                    <td class="px-6 py-4 border-b">{{ $category->products->count() }}</td>
                    <td class="px-6 py-4 border-b text-right">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-500 hover:text-blue-600 mr-4">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-600" onclick="return confirm('Are you sure you want to delete this category?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
