# 📊 Room Booking System Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                           ADMIN SIDE                                 │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. Admin Registers Student                                         │
│     └─> StudentController::store()                                  │
│         └─> room_id = null (FORCED)                                 │
│         └─> Student created WITHOUT room ✓                          │
│                                                                      │
│  2. Admin Views Dashboard                                           │
│     └─> DashboardController::index()                                │
│         └─> Fetches pending_bookings                                │
│         └─> Shows alert banner if bookings exist                    │
│                                                                      │
│  ┌───────────────────────────────────────────────────────┐          │
│  │  ⚠️ PENDING BOOKINGS ALERT                           │          │
│  │  ┌─────────────────────────────────────────────────┐ │          │
│  │  │ Student: John Doe (ST12345)                     │ │          │
│  │  │ Room: 201 in Lincoln Hostel                     │ │          │
│  │  │ Amount: ₦50,000.00                              │ │          │
│  │  │ Occupancy: 2/3 (1 left)                         │ │          │
│  │  │ [✓ Approve] [✗ Reject] [👁 View]               │ │          │
│  │  └─────────────────────────────────────────────────┘ │          │
│  └───────────────────────────────────────────────────────┘          │
│                                                                      │
│  3. Admin Clicks Approve                                            │
│     └─> PaymentController::approve($payment)                        │
│         │                                                            │
│         ├─> VALIDATION CHECKS:                                      │
│         │   ├─> Room exists? ✓                                      │
│         │   ├─> Student already has room? ✗                         │
│         │   ├─> $room->refresh() (get latest data)                 │
│         │   └─> Room has capacity? ✓                                │
│         │                                                            │
│         ├─> START TRANSACTION                                       │
│         │   ├─> Update payment status = 'completed'                 │
│         │   ├─> Assign room_id to student                           │
│         │   ├─> Set check_in_date = now()                           │
│         │   ├─> Set hostel_fee_amount & hostel_fee_paid             │
│         │   ├─> Increment room->occupied                            │
│         │   ├─> Update room->status if full                         │
│         │   ├─> Create AttendanceRecord (check-in)                  │
│         │   └─> Log to laravel.log                                  │
│         │                                                            │
│         ├─> COMMIT TRANSACTION ✓                                    │
│         │                                                            │
│         └─> Notify student (success message)                        │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────┐
│                          STUDENT SIDE                                │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  1. Student Logs In                                                 │
│     └─> student/login                                               │
│                                                                      │
│  2. Browse Hostels                                                  │
│     └─> StudentsDashboardController::hostels()                      │
│         └─> Filters by student gender                               │
│         └─> Shows active hostels                                    │
│                                                                      │
│  ┌───────────────────────────────────────────────────────┐          │
│  │  HOSTEL LIST                                          │          │
│  │  ┌─────────────────────────────────────────────────┐ │          │
│  │  │ 🏠 Lincoln Hostel (Male)                        │ │          │
│  │  │ 📍 123 Campus Road                              │ │          │
│  │  │ ✓ 5 Available Rooms                             │ │          │
│  │  │ [View Details →]                                 │ │          │
│  │  └─────────────────────────────────────────────────┘ │          │
│  └───────────────────────────────────────────────────────┘          │
│                                                                      │
│  3. View Hostel Details                                             │
│     └─> StudentsDashboardController::showHostel($hostel)            │
│         └─> Loads rooms where status = 'available'                  │
│         └─> Filters: occupied < capacity                            │
│                                                                      │
│  ┌───────────────────────────────────────────────────────┐          │
│  │  ROOM 201 DETAILS                                     │          │
│  │  ┌─────────────────────────────────────────────────┐ │          │
│  │  │ Room Type: Triple Room                          │ │          │
│  │  │ Floor: 2nd Floor                                 │ │          │
│  │  │                                                  │ │          │
│  │  │ Occupancy:                                       │ │          │
│  │  │ ┌─────────────────────────────┐                 │ │          │
│  │  │ │ 2 occupied, 1 left          │                 │ │          │
│  │  │ │ [████████░░] 67%            │                 │ │          │
│  │  │ └─────────────────────────────┘                 │ │          │
│  │  │                                                  │ │          │
│  │  │ Payment Plans:                                   │ │          │
│  │  │ ○ Semester: ₦50,000.00                          │ │          │
│  │  │ ○ Full Year: ₦90,000.00                         │ │          │
│  │  │                                                  │ │          │
│  │  │ [💳 Proceed to Payment]                         │ │          │
│  │  └─────────────────────────────────────────────────┘ │          │
│  └───────────────────────────────────────────────────────┘          │
│                                                                      │
│  4. Submit Booking                                                  │
│     └─> StudentsDashboardController::bookRoom($room)                │
│         │                                                            │
│         ├─> VALIDATION CHECKS:                                      │
│         │   ├─> Gender matches? ✓                                   │
│         │   ├─> Room available? ✓                                   │
│         │   ├─> $room->refresh() (get latest data)                 │
│         │   ├─> Room has capacity? ✓                                │
│         │   ├─> Student has room already? ✗                         │
│         │   └─> Student has pending booking? ✗                      │
│         │                                                            │
│         └─> Redirect to payment upload                              │
│                                                                      │
│  5. Upload Payment Receipt                                          │
│     └─> StudentsDashboardController::submitBookingPayment()         │
│         ├─> Create Payment record                                   │
│         │   ├─> student_id                                          │
│         │   ├─> room_id                                             │
│         │   ├─> payment_plan (semester/year)                        │
│         │   ├─> amount                                              │
│         │   ├─> receipt_path (uploaded file)                        │
│         │   └─> status = 'pending'                                  │
│         │                                                            │
│         ├─> Notify ALL Admins                                       │
│         │   └─> "🏠 New Room Booking Payment Received"              │
│         │                                                            │
│         └─> Redirect to dashboard                                   │
│             └─> "Booking submitted! We'll notify you."              │
│                                                                      │
│  6. Wait for Approval...                                            │
│                                                                      │
│  7. Get Notification                                                │
│     └─> "Room Successfully Assigned! 🎉"                            │
│                                                                      │
│  8. View Room Details in Dashboard                                 │
│     └─> Room number, hostel, roommates, etc.                        │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

## Database Transaction Flow

```
┌─────────────────────────────────────────────────────────────┐
│  PaymentController::approve($payment)                       │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  1. VALIDATION PHASE                                        │
├─────────────────────────────────────────────────────────────┤
│  if (!$room)          → Error: "Room not found"            │
│  if ($student->room)  → Error: "Already has room"          │
│  $room->refresh()     → Get latest data from DB            │
│  if ($room->occupied >= $room->capacity)                   │
│     → Mark payment as 'failed'                              │
│     → Notify student about full room                        │
│     → Return error                                          │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼ All checks passed
┌─────────────────────────────────────────────────────────────┐
│  2. TRANSACTION BEGINS                                      │
│     DB::transaction(function() { ... })                     │
└─────────────────────────────────────────────────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        ▼                  ▼                  ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│ Update       │  │ Update       │  │ Update       │
│ Payment      │  │ Student      │  │ Room         │
├──────────────┤  ├──────────────┤  ├──────────────┤
│ status =     │  │ room_id = X  │  │ occupied++   │
│ 'completed'  │  │ check_in =   │  │              │
│              │  │ now()        │  │ if full:     │
│              │  │ fee_amount   │  │  status =    │
│              │  │ fee_paid     │  │  'full'      │
│              │  │ fee_status   │  │              │
└──────────────┘  └──────────────┘  └──────────────┘
        │                  │                  │
        └──────────────────┼──────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  3. CREATE ATTENDANCE RECORD                                │
├─────────────────────────────────────────────────────────────┤
│  AttendanceRecord::create([                                 │
│    'student_id' => $student->id,                            │
│    'type' => 'check_in',                                    │
│    'recorded_at' => now(),                                  │
│    ...                                                      │
│  ])                                                         │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  4. COMMIT TRANSACTION ✓                                    │
│     (All changes saved atomically)                          │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  5. POST-TRANSACTION ACTIONS                                │
├─────────────────────────────────────────────────────────────┤
│  • Notify student (success)                                 │
│  • Log to laravel.log                                       │
│  • Return success message to admin                          │
└─────────────────────────────────────────────────────────────┘
```

## Data Model Relationships

```
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│   Student    │────────▶│     Room     │────────▶│    Hostel    │
├──────────────┤ room_id ├──────────────┤hostel_id├──────────────┤
│ id           │         │ id           │         │ id           │
│ room_id      │         │ hostel_id    │         │ name         │
│ full_name    │         │ room_number  │         │ type         │
│ email        │         │ capacity     │         │ address      │
│ gender       │         │ occupied     │         │ status       │
│ ...          │         │ status       │         │ ...          │
└──────────────┘         │ ...          │         └──────────────┘
       │                 └──────────────┘
       │ student_id             │ room_id
       │                        │
       ▼                        ▼
┌──────────────┐         ┌──────────────┐
│   Payment    │         │  Attendance  │
├──────────────┤         │   Record     │
│ id           │         ├──────────────┤
│ student_id   │         │ student_id   │
│ room_id      │         │ type         │
│ amount       │         │ recorded_at  │
│ status       │         │ ...          │
│ payment_plan │         └──────────────┘
│ receipt_path │
│ ...          │
└──────────────┘
```

## Occupancy Calculation

```
Room Capacity = 3

State 1: Empty Room
┌─────────────────────────────────────┐
│  occupied = 0                       │
│  available_slots = 3 - 0 = 3        │
│  status = 'available'               │
│  Display: "0 occupied, 3 left"      │
│  Progress: [░░░░░░░░░░] 0%          │
└─────────────────────────────────────┘

State 2: First Student Approved
┌─────────────────────────────────────┐
│  occupied = 1                       │
│  available_slots = 3 - 1 = 2        │
│  status = 'available'               │
│  Display: "1 occupied, 2 left"      │
│  Progress: [███░░░░░░░] 33%         │
└─────────────────────────────────────┘

State 3: Second Student Approved
┌─────────────────────────────────────┐
│  occupied = 2                       │
│  available_slots = 3 - 2 = 1        │
│  status = 'available'               │
│  Display: "2 occupied, 1 left"      │
│  Progress: [██████░░░░] 67%         │
│  Color: Yellow (warning)            │
└─────────────────────────────────────┘

State 4: Third Student Approved
┌─────────────────────────────────────┐
│  occupied = 3                       │
│  available_slots = 3 - 3 = 0        │
│  status = 'full' (auto-updated)     │
│  Display: "3 occupied, 0 left"      │
│  Progress: [██████████] 100%        │
│  Color: Red (full)                  │
│  ⚠️ No longer shows in available    │
└─────────────────────────────────────┘
```

## Validation Layers

```
┌─────────────────────────────────────────────────────────────┐
│  LAYER 1: Student View (Frontend)                          │
├─────────────────────────────────────────────────────────────┤
│  • Only show rooms where: status = 'available'              │
│  • Only show rooms where: occupied < capacity               │
│  • Visual indicators (green/yellow/red)                     │
│  • Disabled button if full                                  │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  LAYER 2: Booking Submission (Backend)                     │
├─────────────────────────────────────────────────────────────┤
│  bookRoom() method:                                         │
│  ✓ Gender matches hostel type                              │
│  ✓ Room status = 'available'                               │
│  ✓ $room->refresh() (latest data)                          │
│  ✓ occupied < capacity                                     │
│  ✓ Student doesn't have room                               │
│  ✓ Student doesn't have pending booking                    │
└─────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  LAYER 3: Payment Approval (Final Check)                   │
├─────────────────────────────────────────────────────────────┤
│  approve() method:                                          │
│  ✓ Room exists                                              │
│  ✓ Student doesn't have room                               │
│  ✓ $room->refresh() (absolutely latest data)               │
│  ✓ occupied < capacity (final check)                       │
│  ✓ Database transaction (atomic)                           │
└─────────────────────────────────────────────────────────────┘
```

## Security Features

```
┌────────────────────────────────────────────────────────────┐
│  🔒 PREVENTION MECHANISMS                                  │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  ✓ Race Condition Protection                              │
│    └─> $room->refresh() gets latest DB state              │
│                                                            │
│  ✓ Double-Booking Prevention                              │
│    └─> Check for existing pending payments                │
│                                                            │
│  ✓ Overbooking Prevention                                 │
│    └─> Multiple validation layers                         │
│    └─> Final check in transaction                         │
│                                                            │
│  ✓ Atomic Operations                                      │
│    └─> DB::transaction() ensures all-or-nothing           │
│                                                            │
│  ✓ Role-Based Access Control                              │
│    └─> Admin middleware for admin routes                  │
│    └─> Student middleware for student routes              │
│                                                            │
│  ✓ Input Validation                                       │
│    └─> Laravel validation rules                           │
│    └─> Type checking                                      │
│                                                            │
│  ✓ Audit Trail                                            │
│    └─> Comprehensive logging                              │
│    └─> Attendance records                                 │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

## Files & Responsibilities

```
app/Http/Controllers/
├── StudentController.php
│   └─> Admin registers students (NO room assignment)
│
├── StudentsDashboardController.php
│   ├─> hostels() - Browse hostels
│   ├─> showHostel() - View rooms with occupancy
│   ├─> bookRoom() - Validate & redirect to payment
│   └─> submitBookingPayment() - Create payment record
│
├── PaymentController.php
│   ├─> approve() - Process approval with validation
│   └─> reject() - Mark payment as failed
│
└── DashboardController.php
    └─> index() - Show pending bookings to admin

app/Models/
├── Room.php
│   ├─> getAvailableSlotsAttribute()
│   ├─> canAcceptBooking()
│   └─> getOccupancyPercentageAttribute()
│
├── Payment.php
│   └─> room() relationship
│
└── Student.php
    └─> room() relationship

resources/views/
├── admin/dashboard.blade.php
│   └─> Pending bookings alert & table
│
└── student/hostels/show.blade.php
    └─> Room occupancy display with visual indicators
```

---

**Everything is wired together with proper validation, transactions, and error handling! 🎉**
