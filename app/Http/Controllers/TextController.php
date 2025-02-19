<?php

namespace App\Http\Controllers;

use App\Models\Text;
use GuzzleHttp\Client;
use Auth;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Smalot\PdfParser\Parser;
use Validator;

class TextController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function check(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:200|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data not valid', 'errors' => $validator->errors()], 422);
        }


        $content = $request->content;
        $prompt = "Analyze the following text and determine the likelihood that it was generated by AI. 
        Return only a number from 0 to 100 representing the AI-written percentage. 
        Do not include any other words, symbols, or explanations.  
        Text: " . $content;

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $text = new Text();
        $text->user_id = 1;
        $text->content = $request->content;
        $text->result = $result->choices[0]->message->content;
        $text->save();



        return response()->json(['data' => $result->choices[0]->message->content]);
    }

    public function scan(Request $request)
    {
        $file = $request->file('pdf');
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $content = $pdf->getText();
        $prompt = "Analyze the following text and determine the likelihood that it was generated by AI. 
        Return only a number from 0 to 100 representing the AI-written percentage. 
        Do not include any other words, symbols, or explanations.  
        Text: " . $content;

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
        return response()->json(['data' => $result->choices[0]->message->content,'content' => $content]);

    }
}
