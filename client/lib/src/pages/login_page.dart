import 'package:flutter/material.dart';
import 'package:client/src/services/api_service.dart';
import 'package:client/src/components/password_popup.dart';
import '../components/phone_input.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  // ignore: library_private_types_in_public_api
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  // final ApiService _apiService = ApiService();
  final ApiService _apiService = ApiService(token: '');
  final TextEditingController _phoneController = TextEditingController();
  String _errorMessage = '';

  void _showPasswordPopup() {
    showDialog(
      context: context,
      builder: (BuildContext context) => PasswordPopup(
        onPasswordEntered: (password) async {
          await _login(_phoneController.text, password);
        },
      ),
    );
  }

  Future<void> _login(String phone, String password) async {
    setState(() {
      _errorMessage = ''; // Clear previous errors
    });

    try {
      var response = await _apiService.post('/clients/login', {
        'telephone': phone,
        'mot_de_passe': password,
      });

      // ignore: avoid_print
      // print(response); // Debug: Check the response content

      if (response['token'] != null) {
        // Récupérer le token
        String token = response['token'];

        // Créer une instance d'ApiService en passant le token
        // ignore: unused_local_variable
        final ApiService apiService = ApiService(token: token);
        // ignore: use_build_context_synchronously
        Navigator.pushReplacementNamed(context, '/home', arguments: token);
      } else {
        setState(() {
          _errorMessage =
              'Erreur : accès refusé. Veuillez vérifier vos identifiants.';
        });
      }
    } catch (e) {
      setState(() {
        _errorMessage = e
            .toString()
            .replaceAll('Exception: ', ''); // Display the error message
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Connexion'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.person, size: 100, color: Colors.blue),
            const SizedBox(height: 20),
            const Text(
              'Bienvenue ! Veuillez saisir votre numéro de téléphone pour commencer',
              style: TextStyle(fontSize: 18),
              textAlign: TextAlign.center,
            ),
            // const SizedBox(height: 20),
            const SizedBox(height: 15),
            PhoneInput(controller: _phoneController),
            const SizedBox(height: 20),
            ElevatedButton(
              onPressed: _showPasswordPopup,
              child: const Text('Valider'),
            ),
            const SizedBox(height: 10),
            if (_errorMessage.isNotEmpty) // Display error message if present
              Text(
                _errorMessage,
                style: const TextStyle(color: Colors.red),
                textAlign: TextAlign.center,
              ),
          ],
        ),
      ),
    );
  }
}
