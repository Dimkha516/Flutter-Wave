import 'package:client/src/pages/home_page.dart';
import 'package:client/src/services/auth_service.dart';
import 'package:flutter/material.dart';
import 'src/pages/login_page.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Transfert App',
      theme: ThemeData(primarySwatch: Colors.blue),
      initialRoute: '/',
      home: const LoginPage(),
      routes: {
        '/home': (context) {
          final token = ModalRoute.of(context)!.settings.arguments
              as String; // Récupérer le token
          return HomePage(
            token: token,
            authService: AuthService(token: token),
          ); // Passer le token à la HomePage
        },
      },
    );
  }
}
