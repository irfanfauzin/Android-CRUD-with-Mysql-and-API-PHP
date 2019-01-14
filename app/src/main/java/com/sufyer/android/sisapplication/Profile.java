package com.sufyer.android.sisapplication;

import android.annotation.SuppressLint;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;

public class Profile extends AppCompatActivity {
    private EditText editTextUsername,editTextCurrentpassword,editTextNewpassword,editTextConfirmNewpassword;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);

        User user = SharedPrefManager.getInstance(this).getUser();

        AlertDialog.Builder builder1 = new AlertDialog.Builder(this);
        builder1.setMessage("Are you sure want to delete your account Permanently ?");
        builder1.setCancelable(true);

        builder1.setPositiveButton(
                "Yes",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        SharedPrefManager.getInstance(getApplicationContext()).logout();
                        userDelete();
                    }
                });

        builder1.setNegativeButton(
                "No",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                    }
                });

        final AlertDialog alert11 = builder1.create();


        editTextUsername = (EditText) findViewById(R.id.editTextUsername);
        editTextCurrentpassword = (EditText) findViewById(R.id.editTextCurrentPassword);
        editTextNewpassword = (EditText) findViewById(R.id.editTextNewPassword);
        editTextConfirmNewpassword = (EditText) findViewById(R.id.editTextConfirmNewPassword);

        editTextUsername.setEnabled(false);

        editTextUsername.setText(user.getUsername());

        findViewById(R.id.buttonChangePassword).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                userChangePassword();
            }
        });

        findViewById(R.id.textLogout).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
                SharedPrefManager.getInstance(getApplicationContext()).logout();
            }
        });

        findViewById(R.id.textDelete).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
               alert11.show();
            }
        });


    }


    private void userChangePassword() {
        //first getting the values
        final String username = editTextUsername.getText().toString();
        final String currentpassword = editTextCurrentpassword.getText().toString().trim();
        final String newpassword = editTextNewpassword.getText().toString().trim();
        final String confirmpassword = editTextConfirmNewpassword.getText().toString().trim();

        if (newpassword.equals(currentpassword  )) {
            editTextNewpassword.setError("New password and current password can not be same");
            editTextNewpassword.requestFocus();
            return;

        }

        if (TextUtils.isEmpty(confirmpassword)) {
            editTextConfirmNewpassword.setError("Enter a confirm password");
            editTextConfirmNewpassword.requestFocus();
            return;
        }else if (!newpassword.equals(confirmpassword)) {
            editTextConfirmNewpassword.setError("Password do not match");
            editTextConfirmNewpassword.requestFocus();
            return;

        }


        //if everything is fine

        @SuppressLint("StaticFieldLeak")
        class UserChangedPasssword extends AsyncTask<Void, Void, String> {

            ProgressBar progressBar;

            @Override
            protected void onPreExecute() {
                super.onPreExecute();
                progressBar = (ProgressBar) findViewById(R.id.progressBar);
                progressBar.setVisibility(View.VISIBLE);
            }

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("username", username);
                params.put("currentpassword", currentpassword);
                params.put("newpassword", newpassword);

                //returing the response
                return requestHandler.sendPostRequest(URLs.URL_CHANGE_PASSWORD, params);
            }

            @Override
            protected void onPostExecute(String s) {
                super.onPostExecute(s);
                progressBar.setVisibility(View.GONE);


                try {
                    //converting response to json object
                    JSONObject obj = new JSONObject(s);

                    //if no error in response
                    if (!obj.getBoolean("error")) {
                        Toast.makeText(getApplicationContext(), obj.getString("message"), Toast.LENGTH_SHORT).show();


                    } else {
                        Toast.makeText(getApplicationContext(), obj.getString("message"), Toast.LENGTH_SHORT).show();
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }


        }

        UserChangedPasssword ucp = new UserChangedPasssword();
        ucp.execute() ;
    }




    private void userDelete() {
        //first getting the values
        final String username = editTextUsername.getText().toString();


        //if everything is fine

        @SuppressLint("StaticFieldLeak")
        class UserRegister extends AsyncTask<Void, Void, String> {

            ProgressBar progressBar;

            @Override
            protected void onPreExecute() {
                super.onPreExecute();
                progressBar = (ProgressBar) findViewById(R.id.progressBar);
                progressBar.setVisibility(View.VISIBLE);
            }

            @Override
            protected String doInBackground(Void... voids) {
                //creating request handler object
                RequestHandler requestHandler = new RequestHandler();

                //creating request parameters
                HashMap<String, String> params = new HashMap<>();
                params.put("username", username);


                //returing the response
                return requestHandler.sendPostRequest(URLs.URL_DELETE_ACCOUNT, params);
            }

            @Override
            protected void onPostExecute(String s) {
                super.onPostExecute(s);
                progressBar.setVisibility(View.GONE);


                try {
                    //converting response to json object
                    JSONObject obj = new JSONObject(s);

                    //if no error in response
                    if (!obj.getBoolean("error")) {
                        Toast.makeText(getApplicationContext(), obj.getString("message"), Toast.LENGTH_SHORT).show();


                        //starting the login activity
                        finish();
                        startActivity(new Intent(Profile.this, Login.class));
                    } else {
                        Toast.makeText(getApplicationContext(), obj.getString("message"), Toast.LENGTH_SHORT).show();
                    }
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }


        }

        UserRegister ur = new UserRegister();
        ur.execute();
    }
}
