<?php
//https://graph.facebook.com/me?fields=email&access_token=EAADLVWxska0BAJge6pVfsgFf8yVh5sWZBi4xAcezlsLYI9Krhy9nZBJYLze2nBEINwWE1yFEviBAkTCQCCVRfRMXtIN9bjKiJbgVw5dHpirvWYdPxZCiCGocT1fmTqQZAYC1mJ5rt5iZAY434g82kpCem4ITspxVBkZBjbMiX2DwZDZD
//https://www.facebook.com/v3.0/dialog/oauth?client_id={app-id}&redirect_uri={redirect-uri}&state={state-param}

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

use App\Services\SocialFacebookAccountService;
use Facebook\Facebook as Facebook;
use Facebook\Exceptions\FacebookResponseException as FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException as FacebookSDKException;

use App\User;
use Auth;
use Illuminate\Support\Str;

class SocialAuthFacebookController extends Controller
{
  /**
   * Create a redirect method to facebook api.
   *
   * @return void
   */
    public function redirect()
    {
      if (!session_id()) {
          session_start();
      }
      $fb = new Facebook([
        'app_id' => '223567751713197', // Replace {app-id} with your app id
        'app_secret' => 'f11f863577685c58728946b1cebe7889',
        'default_graph_version' => 'v2.2',
        ]);

      $helper = $fb->getRedirectLoginHelper();

      $permissions = ['email']; // Optional permissions
      $loginUrl = $helper->getLoginUrl('https://quiet-ridge-76454.herokuapp.com/callback', $permissions);

      echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';


      //return Socialite::driver('facebook')->redirect();
    }

    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function callback(SocialFacebookAccountService $service)
    {
      if (!session_id()) {
          session_start();
      }

      $fb = new Facebook([
        'app_id' => '223567751713197', // Replace {app-id} with your app id
        'app_secret' => 'f11f863577685c58728946b1cebe7889',
        'default_graph_version' => 'v2.2',
        ]);

      $helper = $fb->getRedirectLoginHelper();
      $accessToken = $helper->getAccessToken();
      $url ='https://graph.facebook.com/me?fields=email&access_token='.$accessToken;
      $temp = json_decode(file_get_contents($url));
      var_dump($temp);exit;


      $user = User::where('email', $temp->email)->first();

      if ($user) {
          return response()->json(array('token' => $accessToken), 200);
      }

      if(!$user){
          $input['id'] = Str::uuid();
          $input['name'] = $temp->email;
          $input['email'] = $temp->email;
          $input['password'] = bcrypt('ulo-password');
          $input['fb_token'] = $accessToken;
          User::Create($input);
      }
      return response()->json(array('token' => $accessToken), 200);


      /*
      try {
        $accessToken = $helper->getAccessToken();
      } catch(FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
      } catch(FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
      }

      if (! isset($accessToken)) {
        if ($helper->getError()) {
          header('HTTP/1.0 401 Unauthorized');
          echo "Error: " . $helper->getError() . "\n";
          echo "Error Code: " . $helper->getErrorCode() . "\n";
          echo "Error Reason: " . $helper->getErrorReason() . "\n";
          echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
          header('HTTP/1.0 400 Bad Request');
          echo 'Bad request';
        }
        exit;
      }


      // Logged in
      echo '<h3>Access Token</h3>';
      var_dump($accessToken->getValue());

      // The OAuth 2.0 client handler helps us manage access tokens
      $oAuth2Client = $fb->getOAuth2Client();

      // Get the access token metadata from /debug_token
      $tokenMetadata = $oAuth2Client->debugToken($accessToken);
      echo '<h3>Metadata</h3>';
      var_dump($tokenMetadata);

      // Validation (these will throw FacebookSDKException's when they fail)
      $tokenMetadata->validateAppId('{app-id}'); // Replace {app-id} with your app id
      // If you know the user ID this access token belongs to, you can validate it here
      //$tokenMetadata->validateUserId('123');
      $tokenMetadata->validateExpiration();

      if (! $accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
          $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
          echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
          exit;
        }

        echo '<h3>Long-lived</h3>';
        var_dump($accessToken->getValue());
      }

      $_SESSION['fb_access_token'] = (string) $accessToken;

      // User is logged in with a long-lived access token.
      // You can redirect them to a members-only page.
      //header('Location: https://example.com/members.php');
      */

      /*
      $user = $service->createOrGetUser(Socialite::driver('facebook')->user());
      //var_dump($user);exit;
        auth()->login($user);
        return redirect()->to('/home');
      */
    }
}
