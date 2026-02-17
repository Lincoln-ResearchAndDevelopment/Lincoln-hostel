# 🎯 Quick Testing Guide - Room Booking System

## 🚀 Quick Start Testing (5 Minutes)

### Step 1: Test Admin Cannot Assign Rooms
1. Login as admin
2. Go to: `http://127.0.0.1:8000/students/create`
3. Fill in student details
4. **Notice:** No room selection field appears
5. Register student
6. ✅ **Expected:** Student created WITHOUT room assignment

### Step 2: Test Student Can Browse & Book
1. Login as the student you just created (or existing student)
   - URL: `http://127.0.0.1:8000/student/login`
   - Username: Student's admission number
   - Password: (default: welcome123 or ask admin)

2. Click "Browse Hostels" or go to: `http://127.0.0.1:8000/student/hostels`

3. **Verify you see:**
   - Hostels matching your gender
   - Number of available rooms

4. Click on any hostel to view rooms

5. **Check each room shows:**
   - "X occupied, Y left" (e.g., "2 occupied, 1 left")
   - Progress bar showing occupancy
   - Price for semester and yearly plans

6. Select a room and choose payment plan

7. Upload a payment receipt (any image or PDF file)

8. Submit booking

9. ✅ **Expected:** Success message + redirected to dashboard

### Step 3: Test Admin Sees Notification
1. Login as admin
2. Go to dashboard: `http://127.0.0.1:8000/dashboard`

3. **Look for:**
   - ⚠️ Yellow alert banner at top
   - "X Room Booking(s) Awaiting Approval"
   - Table showing pending bookings with:
     * Student name
     * Room number
     * Hostel name
     * Payment amount
     * Current occupancy (e.g., "2/3 (1 left)")
     * Approve/Reject buttons

4. ✅ **Expected:** Booking appears immediately

### Step 4: Test Approval Process
1. In the pending bookings table, click green "✓" (checkmark) button

2. Confirm the prompt

3. ✅ **Expected Results:**
   - Success message: "Payment approved! Room X has been assigned to [Student Name]"
   - Booking disappears from pending list
   - Room occupancy increases by 1

4. Verify student got room:
   - Go to: `http://127.0.0.1:8000/students`
   - Find the student
   - Check they now have a room assigned

### Step 5: Test Occupancy Updates
1. As admin, go to: `http://127.0.0.1:8000/rooms`

2. Find the room that was just booked

3. ✅ **Verify:** `occupied` count increased

4. If room is now full (occupied = capacity):
   - Status should show "Full"
   - No longer appears in student's available rooms list

---

## 🐛 Common Issues & Solutions

### Issue: "Room field still appears in student registration"
**Solution:** Clear browser cache and refresh page

### Issue: "Student can't see any hostels"
**Solution:** Check student's gender matches hostel type (male/female/mixed)

### Issue: "Occupancy doesn't update"
**Solution:** Refresh the page (or implement WebSockets for real-time)

### Issue: "Payment approval gives error"
**Solution:** Check room isn't already full. System prevents overbooking.

### Issue: "Student sees error when trying to book"
**Possible Reasons:**
- Already has pending booking
- Already has room assigned
- Room just got filled

---

## 📊 What to Look For (Success Criteria)

### ✅ Admin Dashboard Should Show:
- Pending Bookings count in stats
- Yellow warning banner when bookings pending
- Table with booking details
- Real-time occupancy status for each booking
- Disabled approve button if room is full

### ✅ Student View Should Show:
- Clear occupancy numbers ("2 occupied, 1 left")
- Visual progress bar
- Color-coded badges (green/yellow/red)
- Payment plan options
- Clear prices

### ✅ After Approval:
- Student has room assigned
- Room occupancy increased
- Student received notification
- If room full, status = "full"
- Room no longer shows in available list

---

## 🎬 Demo Scenario (Full Workflow)

### Scenario: John Wants to Book a Room

**1. Admin Creates John's Account**
- Admin registers John (Male, Semester 1)
- John gets email with login credentials
- No room is assigned yet

**2. John Logs In**
- Goes to student portal
- Sees dashboard with "Book a Room" option

**3. John Browses Hostels**
- Selects "Male Hostels"
- Sees Lincoln Hostel - 5 rooms available
- Clicks to view details

**4. John Selects Room 201**
- Sees: "Room 201 - 2 occupied, 1 left"
- Progress bar shows 66% full
- Price: ₦50,000 (semester) or ₦90,000 (year)

**5. John Books the Room**
- Chooses "Semester" plan
- Uploads receipt image
- Submits booking
- Gets message: "Submitted! We'll notify you."

**6. Admin Gets Alert**
- Dashboard shows warning banner
- "1 Room Booking Awaiting Approval"
- Table shows John's booking details
- Room 201: 2/3 (1 left)

**7. Admin Reviews & Approves**
- Clicks view receipt (optional)
- Clicks green checkmark
- Confirms approval

**8. System Magic ✨**
- John assigned to Room 201
- Occupancy: 2/3 → 3/3
- Room status: available → full
- John gets notification (can see in dashboard)
- Attendance check-in record created

**9. John Checks Dashboard**
- Sees "Room: 201" in dashboard
- Can view room details
- Sees roommates list
- Success! 🎉

---

## 🔧 Advanced Testing

### Test Double-Booking Prevention
1. As John (with pending booking), try to book another room
2. ✅ **Expected:** Error - "You already have a pending booking"

### Test Full Room Handling
1. Fill a room to capacity
2. Have student submit booking for that room
3. Admin tries to approve
4. ✅ **Expected:** Error or payment marked failed
5. Student gets notification about issue

### Test Rejection Flow
1. Admin clicks red "X" to reject booking
2. ✅ **Expected:**
   - Payment status = "failed"
   - Student gets rejection notification
   - No room assigned

---

## 📞 Support & Debugging

### Useful URLs:
- Admin Dashboard: `http://127.0.0.1:8000/dashboard`
- Student Login: `http://127.0.0.1:8000/student/login`
- Student Dashboard: `http://127.0.0.1:8000/student/dashboard`
- Browse Hostels: `http://127.0.0.1:8000/student/hostels`
- Payments: `http://127.0.0.1:8000/payments`
- Rooms: `http://127.0.0.1:8000/rooms`
- Students: `http://127.0.0.1:8000/students`

### Check Logs:
```bash
# In project directory
tail -f storage/logs/laravel.log
```

Look for entries like:
```
Room booking approved and assigned
student_id: 123
room_id: 45
new_occupancy: 3/3
```

### Database Quick Checks:
```sql
-- Check pending bookings
SELECT * FROM payments WHERE status = 'pending' AND room_id IS NOT NULL;

-- Check room occupancy
SELECT id, room_number, capacity, occupied, status FROM rooms;

-- Check students with rooms
SELECT id, full_name, room_id FROM students WHERE room_id IS NOT NULL;
```

---

## ✅ Final Checklist

Before marking as complete, verify:

- [ ] Admin dashboard shows pending bookings alert
- [ ] Student can browse hostels
- [ ] Occupancy displays correctly ("X occupied, Y left")
- [ ] Student can submit booking
- [ ] Admin receives notification
- [ ] Admin can approve booking
- [ ] Room gets assigned to student
- [ ] Occupancy count updates
- [ ] Student receives confirmation
- [ ] Full rooms marked as "full"
- [ ] Prevents double-booking
- [ ] Handles edge cases (room filling up)

**When all checked:** ✅ System is working perfectly!

---

**Need Help?** Check `IMPLEMENTATION_COMPLETE.md` for detailed documentation.
