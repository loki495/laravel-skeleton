<?php

class Login_Controller extends Base_Controller 
{

	//public $layout = 'layouts.main';
	//public $restful = true;
	/**
	 * Catch-all method for requests that can't be matched.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	public function get_newuser($action = null)
	{
		return View::make('user.register');
	}

	public function post_newuser($action = null)
	{
		$rules = array(
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users',
			'birthdate' => 'required|before:-10 years|after:01-01-1900',
			'password' => 'required',
		);

	    $validation = Validator::make(Input::all(), $rules);
	    if ($validation->fails())
	    {
	    	Input::flash();
	        return Redirect::to('login/newuser')->with_errors($validation)->with_input();
	    }

		$user = new User();
		$user->email = Input::get('email');
		$user->password = Input::get('password');
		$user->name = Input::get('first_name').' '.Input::get('last_name');
		$user->save();

		$profile = array(
			'first_name' => Input::get('first_name'), 
			'birth_date' => Input::get('birthdate'),
			'last_name' => Input::get('last_name')
			);
		$user->profile()->insert($profile);
		return Redirect::to('home');
	}

	public function get_index($action = 'page')
	{
		//echo(var_export($this->layout, true).'<br>');
		//$this->layout = 'layouts.simple';
		//die(var_export($this->layout, true));
		if ($action == 'popup')	{
			$this->layout = 'layouts.empty';
			$this->layout = $this->layout();
		}
		return $this->layout->nest('content', 'user.login');
		//metodi alternativi
		//return View::make($layout)->nest('content', 'user.login');
		//return View::make('user.login');
	}

	public function post_index()
	{
	    $rules = array(
	    'email' => 'required|email',
	    'password' => 'required',
		);

	    $validation = Validator::make(Input::all(), $rules);
	    if ($validation->fails())
	    {
	        return Redirect::to('login')->with_errors($validation);
	    }

		if (Auth::attempt(array('username' => Input::get('email'), 'password' => Input::get('password')))){
			$logged_name = Auth::user()->profile->first_name;
			return Redirect::home()->with('logged_name', $logged_name);
		}
		else
			return "Ma cu si?";
	}

}