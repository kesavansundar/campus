<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Test Creation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f6f9fc 0%, #eef2f7 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .floating-label {
            transform: translateY(-50%) scale(0.85);
            background: white;
            padding: 0 6px;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 py-12">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn = new mysqli('localhost', 'root', '', 'campus_recruitment');
        
        if ($conn->connect_error) {
            die("<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Connection failed: " . $conn->connect_error . "</div>");
        }
        
        try {
            $conn->begin_transaction();
            
            $stmt = $conn->prepare("INSERT INTO mock_tests (test_name, duration) VALUES (?, ?)");
            $stmt->bind_param("si", $_POST['test_name'], $_POST['duration']);
            $stmt->execute();
            $test_id = $conn->insert_id;
            
            $questionStmt = $conn->prepare("INSERT INTO test_questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_answer, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($_POST['questions'] as $key => $question) {
                $questionStmt->bind_param(
                    "issssssi",
                    $test_id,
                    $question,
                    $_POST['options'][0][$key],
                    $_POST['options'][1][$key],
                    $_POST['options'][2][$key],
                    $_POST['options'][3][$key],
                    $_POST['correct_answers'][$key],
                    $_POST['marks'][$key]
                );
                $questionStmt->execute();
            }
            
            $conn->commit();
            echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4'>Test created successfully!</div>";
            
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4'>Error: " . $e->getMessage() . "</div>";
        }
        
        $conn->close();
    }
    ?>

    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Create Mock Test</h1>
                <div class="space-x-4">
                    <a href="view_tests.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        View All Tests
                    </a>
                    <a href="admin_index.html" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                        Admin Index Page
                    </a>
                </div>
            </div>

            <form method="POST" class="glass-card rounded-xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="relative">
                        <label class="absolute -top-3 left-4 floating-label text-blue-600">Test Name</label>
                        <input type="text" name="test_name" required 
                               class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>
                    <div class="relative">
                        <label class="absolute -top-3 left-4 floating-label text-blue-600">Duration (minutes)</label>
                        <input type="number" name="duration" required 
                               class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>
                </div>

                <div id="questions-container">
                    <div class="question-block mb-8 p-6 border border-gray-200 rounded-xl">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-800">Question 1</h3>
                            <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-gray-700">Question Text</label>
                            <textarea name="questions[]" required rows="3"
                                      class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block mb-2 text-gray-700">Option A</label>
                                <input type="text" name="options[0][]" required 
                                       class="input-focus w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </div>
                            <div>
                                <label class="block mb-2 text-gray-700">Option B</label>
                                <input type="text" name="options[1][]" required 
                                       class="input-focus w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </div>
                            <div>
                                <label class="block mb-2 text-gray-700">Option C</label>
                                <input type="text" name="options[2][]" required 
                                       class="input-focus w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </div>
                            <div>
                                <label class="block mb-2 text-gray-700">Option D</label>
                                <input type="text" name="options[3][]" required 
                                       class="input-focus w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-gray-700">Correct Answer</label>
                                <select name="correct_answers[]" required 
                                        class="input-focus w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2 text-gray-700">Marks</label>
                                <input type="number" name="marks[]" value="1" required 
                                       class="input-focus w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-8">
                    <button type="button" onclick="addQuestion()" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Add Another Question
                    </button>
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                        Create Test
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addQuestion() {
            const questionsContainer = document.getElementById('questions-container');
            const questionBlocks = document.querySelectorAll('.question-block');
            const newIndex = questionBlocks.length + 1;

            const newQuestionBlock = questionBlocks[0].cloneNode(true);
            newQuestionBlock.querySelector('h3').innerText = `Question ${newIndex}`;
            newQuestionBlock.querySelectorAll('textarea, input').forEach(input => input.value = '');
            questionsContainer.appendChild(newQuestionBlock);
        }

        function removeQuestion(button) {
            const questionBlock = button.closest('.question-block');
            const questionsContainer = document.getElementById('questions-container');
            if (questionsContainer.children.length > 1) {
                questionBlock.remove();
            }
        }
    </script>
</body>
</html>
