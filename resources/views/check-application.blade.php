<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Application Status - LincHostel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .check-container {
            max-width: 500px;
            width: 100%;
        }
        
        .header-card {
            background: linear-gradient(135deg, #cc0000, #990000);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(204, 0, 0, 0.3);
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #cc0000;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #cc0000;
            box-shadow: 0 0 0 3px rgba(204, 0, 0, 0.1);
        }
        
        .btn-check {
            background: linear-gradient(135deg, #cc0000, #990000);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-check:hover {
            background: linear-gradient(135deg, #990000, #660000);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(204, 0, 0, 0.3);
        }
        
        .info-card {
            background: #e8f4fd;
            border: 2px solid #17a2b8;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #cc0000;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .example-format {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="check-container">
        <!-- Header -->
        <div class="header-card">
            <h1><i class="fas fa-home me-2"></i>LincHostel</h1>
            <h3>Check Application Status</h3>
            <p class="mb-0">Enter your application number to check the status</p>
        </div>

        <!-- Form -->
        <div class="form-card">
            <form id="checkForm" onsubmit="checkApplication(event)">
                <div class="form-group">
                    <label for="application_number">
                        <i class="fas fa-hashtag me-2"></i>Application Number
                    </label>
                    <input type="text" 
                           id="application_number" 
                           name="application_number" 
                           placeholder="Enter your application number" 
                           required>
                    <div class="example-format">
                        <strong>Format example:</strong> LH202601001, LH202601002, etc.
                    </div>
                </div>
                
                <button type="submit" class="btn-check">
                    <i class="fas fa-search me-2"></i>Check Status
                </button>
            </form>

            <!-- Info Card -->
            <div class="info-card">
                <h6><i class="fas fa-info-circle me-2"></i>How to find your application number:</h6>
                <ul class="mb-0">
                    <li>Check the confirmation email sent after submitting your application</li>
                    <li>Look for the application number in the format: LH + Year + Month + Sequential Number</li>
                    <li>Contact the hostel office if you can't find your application number</li>
                </ul>
            </div>
        </div>

        <!-- Back Link -->
        <div class="back-link">
            <a href="/"><i class="fas fa-arrow-left me-2"></i>Back to Home</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function checkApplication(event) {
            event.preventDefault();
            
            const applicationNumber = document.getElementById('application_number').value.trim();
            
            if (!applicationNumber) {
                alert('Please enter your application number');
                return;
            }
            
            // Redirect to application details page
            window.location.href = `/application/${applicationNumber}`;
        }
        
        // Auto-format application number as user types
        document.getElementById('application_number').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            
            // Remove any non-alphanumeric characters except for the format we want
            value = value.replace(/[^A-Z0-9]/g, '');
            
            e.target.value = value;
        });
    </script>
</body>
</html>