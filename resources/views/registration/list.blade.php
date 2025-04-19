@extends('layouts/contentNavbarLayout')
@section('title', 'User Management')
@section('content')

@if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
@endif

<div class="container mt-5">
    <h2>Registered Users</h2>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Encrypted ID</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ Crypt::encryptString($user->id) }}</td> 
                <td>{{ $user->username }}</td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
            
                <td>
               
                    <a href="#" class="btn btn-primary btn-sm"
                       data-bs-toggle="modal"
                       data-bs-target="#editUserModal"
                       data-user-id="{{Crypt::encryptString($user->id)}}"
                       data-username="{{ $user->username }}"
                       data-firstname="{{ $user->first_name }}"
                       data-lastname="{{ $user->last_name }}">
                       <i class="bx bx-edit"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>



<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editUserForm" method="POST" action="{{ route('registration.update') }}"> 
        @csrf
        @method('PUT')
        <div class="modal-body">
        
          <input type="hidden" name="id" id="userId">

          <div class="row">
            <div class="col mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" id="username" name="username" class="form-control" placeholder="Enter Username" required>
            </div>
          </div>

          <div class="row g-2">
            <div class="col mb-0">
              <label for="firstName" class="form-label">First Name</label> 
              <input type="text" id="firstName" name="first_name" class="form-control" placeholder="Enter First Name" required>
            </div>
            <div class="col mb-0">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" id="lastName" name="last_name" class="form-control" placeholder="Enter Last Name" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>




<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editLinks = document.querySelectorAll('[data-bs-target="#editUserModal"]');

    editLinks.forEach(link => {
      link.addEventListener('click', function () {
        document.getElementById('userId').value = link.getAttribute('data-user-id');
        document.getElementById('username').value = link.getAttribute('data-username');
        document.getElementById('firstName').value = link.getAttribute('data-firstname');
        document.getElementById('lastName').value = link.getAttribute('data-lastname');
      });
    });
  });
</script>
@endsection
