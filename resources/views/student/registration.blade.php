@extends('layouts.app')
@section('title', 'Semester Course Registration')

@section('content')
<div class="card p-4">
    <h4 class="fw-bold mb-1">Course Registration</h4>
    <p class="text-muted">Active Term: <strong>{{ $activeSemester->name }}</strong></p>

    <form action="{{ route('student.registration.store') }}" method="POST">
        @csrf
        <table class="table table-bordered mt-3 align-middle">
            <thead class="table-light">
                <tr>
                    <th width="40">Select</th>
                    <th>Course Code</th>
                    <th>Title</th>
                    <th>Credit Hours</th>
                    <th>Section</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableOfferings as $offering)
                    @php
                        $isEnrolled = $currentRegistration && $currentRegistration->registrationCourses->pluck('course_offering_id')->contains($offering->id);
                    @endphp
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="offerings[]" value="{{ $offering->id }}" {{ $isEnrolled ? 'checked' : '' }}>
                        </td>
                        <td><strong>{{ $offering->course->code }}</strong></td>
                        <td>{{ $offering->course->title }}</td>
                        <td>{{ $offering->course->credit_hours }} Cr.Hr</td>
                        <td><span class="badge bg-secondary">{{ $offering->section }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-2">Submit Registration</button>
    </form>
</div>
@endsection