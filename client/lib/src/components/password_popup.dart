import 'package:flutter/material.dart';

class PasswordPopup extends StatefulWidget {
  final Function(String) onPasswordEntered;

  const PasswordPopup({super.key, required this.onPasswordEntered});

  @override
  // ignore: library_private_types_in_public_api
  _PasswordPopupState createState() => _PasswordPopupState();
}

class _PasswordPopupState extends State<PasswordPopup> {
  final List<TextEditingController> _controllers =
      List.generate(4, (_) => TextEditingController());

  void _onTextChanged(int index) {
    if (_controllers[index].text.length == 1 && index < 3) {
      FocusScope.of(context).nextFocus();
    }
  }

  void _submitPassword() {
    String password = _controllers.map((c) => c.text).join();
    widget.onPasswordEntered(password);
    Navigator.of(context).pop();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Entrez votre code PIN'),
      content: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: List.generate(4, (index) {
          return SizedBox(
            width: 40,
            child: TextField(
              controller: _controllers[index],
              obscureText: true,
              maxLength: 1,
              keyboardType: TextInputType.number,
              onChanged: (value) => _onTextChanged(index),
              decoration: const InputDecoration(counterText: ''),
            ),
          );
        }),
      ),
      actions: [
        TextButton(
          onPressed: _submitPassword,
          child: const Text('Valider'),
        ),
      ],
    );
  }
}
