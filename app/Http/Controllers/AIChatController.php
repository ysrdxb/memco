<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class AIChatController extends Controller
{
    public function index()
    {
        return view('ai-chat.index');
    }

    public function sendMessage(Request $request)
    {
        $apiKey = Setting::where('key', 'groq_api_key')->value('value');
        if (!$apiKey) {
            return response()->json(['error' => 'Groq API Key is not configured in settings.']);
        }

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are MEMCO AI Assistant, a highly intelligent conversational assistant for a construction management ERP. 
CORE MODULES:
1. Projects (Look up locations, codes, names)
2. Products (Look up materials, inventory items)
3. Stock (Requires a Product ID! Can optionally filter by Project ID)
4. Transfers / Material Requests (MR) (Requires MR/Transfer No, or Project ID to filter)
5. LPO / Purchases (Purchase orders)

RULES FOR AMBIGUITY, INTERROGATION & SPEECH RECOGNITION:
- SPEECH CORRECTION: The user is speaking through a flawed Speech-to-Text engine. Their prompt may contain phonetically garbled words (e.g. "regionalu" instead of "Jebel Ali", "ancient" instead of "any ten", "demak" instead of "damac"). You are an AI: ALWAYS phonetically interpret and correct their intent BEFORE searching!
- NEVER GUESS BLINDLY. If the user asks for "stock for damac", you CANNOT check stock without a product. You MUST first search the projects for "damac", confirm it, and reply: "I found the project. Which product\'s stock are you looking for?"
- If a search (e.g. project or product) is ambiguous and returns multiple options, present them as clickable buttons using this exact syntax: `[SELECT: Name]`. For example: "Which one did you mean? \n\n[SELECT: Damac Villas]\n[SELECT: Damac Towers]"
- If the user provides a product name, you must search for the product FIRST to get its ID, then use that ID to check stock.
- Do NOT call the same tool multiple times if it fails to find data.
- NEVER attempt to loop through and look up data for multiple items one-by-one. If a user asks for "10 products", just look up 1 or 2 and tell them you can only do a few at a time.

IMPORTANT FORMATTING:
- When presenting final records, ALWAYS format them into a clean Markdown table with appropriate column headers. Never output data as raw bullet points if a table is better.
- Tools often return `total_matching_records`. ALWAYS mention this number to the user so they know how many total items exist, and explicitly state that you are only showing the top results to save space.
- Use `[SELECT: Option]` syntax ONLY when asking the user to choose one item from a list to proceed.'
            ]
        ];

        // Format history
        foreach ($history as $msg) {
            if ($msg['role'] !== 'system') {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_products',
                    'description' => 'Search the product catalog by name, category, or code. Can also be called without arguments to get recent products.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Optional. The search term'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_projects',
                    'description' => 'Search projects by name or code. Can also be called without arguments to get recent projects.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Optional. The search term'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_stock_levels',
                    'description' => 'Check stock levels. Can check by product_id across all locations, or check all stock inside a specific project_id.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'product_id' => [
                                'type' => 'integer',
                                'description' => 'Optional. The ID of the product'
                            ],
                            'project_id' => [
                                'type' => 'integer',
                                'description' => 'Optional. The ID of the project'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_purchases',
                    'description' => 'Search for LPO (Local Purchase Orders) by LPO number or reference. Can also be called without arguments to get recent LPOs.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'lpo_no' => [
                                'type' => 'string',
                                'description' => 'Optional. The LPO Number or reference to search'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_material_requests',
                    'description' => 'Search for Material Requests (MR) by MR number, reference, or project. Can also be called without arguments to get recent material requests.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'mr_no' => [
                                'type' => 'string',
                                'description' => 'Optional. The MR Number or reference to search'
                            ],
                            'project_id' => [
                                'type' => 'integer',
                                'description' => 'Optional. Filter by specific Project ID'
                            ],
                            'project_name' => [
                                'type' => 'string',
                                'description' => 'Optional. Filter by Project Name (if Project ID is unknown)'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_transfers',
                    'description' => 'Search for Transfers by Transfer number, reference, or project. Can also be called without arguments to get recent transfers.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'transfer_no' => [
                                'type' => 'string',
                                'description' => 'Optional. The Transfer Number or reference to search'
                            ],
                            'project_id' => [
                                'type' => 'integer',
                                'description' => 'Optional. Filter by specific Project ID'
                            ],
                            'project_name' => [
                                'type' => 'string',
                                'description' => 'Optional. Filter by Project Name (if Project ID is unknown)'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $iterations = 0;
        $retries = 0;
        $previousToolCallsSignature = null;
        
        while ($iterations < 5 && $retries < 3) {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-20b',
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => 'auto',
                'temperature' => 0.1
            ]);

            $responseData = $response->json();
            
            if (isset($responseData['error'])) {
                $errorMsg = $responseData['error']['message'] ?? '';
                if (strpos($errorMsg, 'Rate limit reached') !== false) {
                    $retries++;
                    sleep(4);
                    continue; // Wait 4 seconds and try again
                }
                return response()->json(['error' => $errorMsg ?: 'Unknown Groq Error']);
            }

            $responseMessage = $responseData['choices'][0]['message'] ?? null;

            if (!$responseMessage) {
                return response()->json(['error' => 'Invalid response from Groq.']);
            }

            // If the model called a tool
            if (isset($responseMessage['tool_calls'])) {
                // Prevent infinite looping with the exact same tool calls
                $currentToolCallsSignature = [];
                foreach ($responseMessage['tool_calls'] as $tc) {
                    $currentToolCallsSignature[] = $tc['function']['name'] . $tc['function']['arguments'];
                }
                $currentToolCallsSignatureStr = implode('|', $currentToolCallsSignature);

                if ($currentToolCallsSignatureStr === $previousToolCallsSignature) {
                    return response()->json(['reply' => 'I tried searching the database, but could not find the exact records you requested. Please try rephrasing your question or checking the spelling.']);
                }
                $previousToolCallsSignature = $currentToolCallsSignatureStr;
                // Append the tool call to history cleanly, stripping proprietary tags like 'reasoning'
                $cleanMessage = [
                    'role' => $responseMessage['role'],
                    'content' => $responseMessage['content'] ?? null,
                    'tool_calls' => $responseMessage['tool_calls']
                ];
                $messages[] = $cleanMessage; 

                foreach ($responseMessage['tool_calls'] as $toolCall) {
                    $functionName = $toolCall['function']['name'];
                    $args = json_decode($toolCall['function']['arguments'], true);
                    
                    $functionResult = $this->executeTool($functionName, $args);
                    
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'name' => $functionName,
                        'content' => json_encode($functionResult)
                    ];
                }
                
                $iterations++;
            } else {
                // If no tool was called, return the direct reply
                return response()->json(['reply' => $responseMessage['content'] ?? 'Sorry, I could not generate an answer.']);
            }
        }

        return response()->json(['reply' => "Task too complex: Maximum AI reasoning steps reached.\n\nDebug Trace:\n```json\n" . json_encode(array_slice($messages, -6), JSON_PRETTY_PRINT) . "\n```"]);
    }

    public function refineSpeech(Request $request)
    {
        $message = $request->input('message');
        if (!$message) {
            return response()->json(['reply' => '']);
        }

        $apiKey = config('services.groq.key');
        if (!$apiKey) {
            return response()->json(['reply' => $message]);
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a phonetic auto-corrector for a UAE construction ERP. Fix spelling errors in the user\'s raw speech-to-text input based on construction context. 
Common corrections:
- "jubila" / "regionalu" -> "Jebel Ali"
- "the mark" / "demak" -> "Damac"
- "lpu" -> "LPO"
- "mrm" -> "Material Request"
- "ancient" -> "any ten"
- "cutter knight" -> "cutter knife"
Reply with ONLY the perfectly corrected text. Do NOT answer the question. Do NOT add quotes or explanations.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ];

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-20b', // Fast and cheap model
                'messages' => $messages,
                'temperature' => 0.1
            ]);

            $responseData = $response->json();
            
            if (isset($responseData['choices'][0]['message']['content'])) {
                $refinedText = trim($responseData['choices'][0]['message']['content']);
                // Remove surrounding quotes if the AI adds them despite instructions
                $refinedText = trim($refinedText, '"\'');
                return response()->json(['reply' => $refinedText]);
            }
        } catch (\Exception $e) {
            // Ignore error and just return original message on failure
        }

        return response()->json(['reply' => $message]);
    }

    private function executeTool($name, $args)
    {
        try {
            if ($name === 'search_products') {
                $q = trim($args['query'] ?? '');
                $query = Product::where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%");
                
                $firstWord = $q ? explode(' ', $q)[0] : '';
                if ($firstWord && strlen($firstWord) >= 3) {
                    $query->orWhereRaw("SOUNDEX(SUBSTRING_INDEX(name, ' ', 1)) = SOUNDEX(?)", [$firstWord]);
                }

                $total = $query->count();
                $results = $query->select('id', 'name', 'code', 'category_id', 'price', 'unit_id')
                    ->limit(10)->get()->toArray();
                    
                return ['total_matching_records' => $total, 'showing_top_results' => count($results), 'data' => $results];
            }

            if ($name === 'search_projects') {
                $q = trim($args['query'] ?? '');
                $query = Project::where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%");
                
                $firstWord = $q ? explode(' ', $q)[0] : '';
                if ($firstWord && strlen($firstWord) >= 3) {
                    $query->orWhereRaw("SOUNDEX(SUBSTRING_INDEX(name, ' ', 1)) = SOUNDEX(?)", [$firstWord]);
                }

                $total = $query->count();
                $results = $query->select('id', 'name', 'code', 'address')
                    ->limit(10)->get()->toArray();

                return ['total_matching_records' => $total, 'showing_top_results' => count($results), 'data' => $results];
            }

            if ($name === 'get_stock_levels') {
                $productId = $args['product_id'] ?? null;
                $projectId = $args['project_id'] ?? null;
                
                if (!$productId && !$projectId) {
                    return ['error' => 'Must provide product_id or project_id. Do not call this tool again without one of these IDs. You must ask the user for the missing information instead.'];
                }

                $results = collect();
                
                if ($productId) {
                    $warehouseQuery = DB::table('stock_records')
                        ->where('product_id', $productId)
                        ->where('stockable_type', 'App\Models\Warehouse')
                        ->join('warehouses', 'stock_records.stockable_id', '=', 'warehouses.id')
                        ->select('warehouses.name as location_name', DB::raw("'Warehouse' as location_type"), 'stock_records.quantity');
                    
                    if (!$projectId) {
                        $results = $results->concat($warehouseQuery->get());
                    }
                    
                    $projectQuery = DB::table('stock_records')
                        ->where('product_id', $productId)
                        ->where('stockable_type', 'App\Models\Project')
                        ->join('projects', 'stock_records.stockable_id', '=', 'projects.id')
                        ->select('projects.name as location_name', DB::raw("'Project' as location_type"), 'stock_records.quantity');
                        
                    if ($projectId) {
                        $projectQuery->where('projects.id', $projectId);
                    }
                    
                    $results = $results->concat($projectQuery->get());
                } elseif ($projectId) {
                    $results = DB::table('stock_records')
                        ->where('stockable_type', 'App\Models\Project')
                        ->where('stockable_id', $projectId)
                        ->join('products', 'stock_records.product_id', '=', 'products.id')
                        ->select('products.name as product_name', 'stock_records.quantity')
                        ->limit(10)
                        ->get();
                }
                
                return $results->toArray();
            }

            if ($name === 'search_purchases') {
                $q = trim($args['lpo_no'] ?? '');
                $projectId = $args['project_id'] ?? null;
                $projectName = trim($args['project_name'] ?? '');

                $query = DB::table('purchases')
                         ->select('purchases.id', 'purchases.ref_no', 'purchases.supplier_id', 'purchases.total_amount', 'purchases.status', 'purchases.date');

                if ($projectId) {
                    $query->join('transfers', 'purchases.transfer_id', '=', 'transfers.id')
                          ->where('transfers.project_id', $projectId);
                } elseif ($projectName) {
                    $query->join('transfers', 'purchases.transfer_id', '=', 'transfers.id')
                          ->join('projects', 'transfers.project_id', '=', 'projects.id')
                          ->where(function($sub) use ($projectName) {
                              $sub->where('projects.name', 'like', "%$projectName%");
                              $firstWord = $projectName ? explode(' ', $projectName)[0] : '';
                              if ($firstWord && strlen($firstWord) >= 3) {
                                  $sub->orWhereRaw("SOUNDEX(SUBSTRING_INDEX(projects.name, ' ', 1)) = SOUNDEX(?)", [$firstWord]);
                              }
                          });
                }

                if ($q) {
                    $query->where(function($sub) use ($q) {
                        $sub->where('purchases.ref_no', 'like', "%$q%")
                            ->orWhere('purchases.purchase_code', 'like', "%$q%");
                    });
                }

                $total = $query->count();
                $results = $query->limit(10)->orderBy('purchases.id', 'desc')->get()->toArray();
                
                return ['total_matching_records' => $total, 'showing_top_results' => count($results), 'data' => $results];
            }

            if ($name === 'search_material_requests' || $name === 'search_transfers') {
                $q = trim($args['mr_no'] ?? $args['transfer_no'] ?? '');
                $projectId = $args['project_id'] ?? null;
                $projectName = trim($args['project_name'] ?? '');

                $query = DB::table('transfers');
                
                if ($projectId) {
                    $query->where('transfers.project_id', $projectId);
                } elseif ($projectName) {
                    $query->join('projects', 'transfers.project_id', '=', 'projects.id')
                          ->where(function($sub) use ($projectName) {
                              $sub->where('projects.name', 'like', "%$projectName%");
                              $firstWord = $projectName ? explode(' ', $projectName)[0] : '';
                              if ($firstWord && strlen($firstWord) >= 3) {
                                  $sub->orWhereRaw("SOUNDEX(SUBSTRING_INDEX(projects.name, ' ', 1)) = SOUNDEX(?)", [$firstWord]);
                              }
                          });
                }

                if ($q) {
                    $query->where(function($sub) use ($q) {
                        $sub->where('transfers.transfer_no', 'like', "%$q%")
                            ->orWhere('transfers.ref_no', 'like', "%$q%");
                    });
                }

                $total = $query->count();
                $results = $query->select('transfers.id', 'transfers.transfer_no', 'transfers.ref_no', 'transfers.status', 'transfers.date')
                    ->limit(10)
                    ->orderBy('transfers.id', 'desc')
                    ->get()->toArray();
                    
                return ['total_matching_records' => $total, 'showing_top_results' => count($results), 'data' => $results];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }

        return ['error' => 'Unknown tool or unhandled execution.'];
    }
}
