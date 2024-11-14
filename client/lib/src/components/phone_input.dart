import 'package:flutter/material.dart';
// import '../utils/validators.dart';

class PhoneInput extends StatelessWidget {
  final TextEditingController controller;

  const PhoneInput({super.key, required this.controller});

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      keyboardType: TextInputType.phone,
      decoration: const InputDecoration(
        labelText: 'Numéro de téléphone',
        prefixIcon: Icon(Icons.phone),
        border: OutlineInputBorder(),
      ),
    );
  }
}
