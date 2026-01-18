INSERT INTO admins (username, password_hash, created_at)
VALUES ('admin', '$2y$12$vxG0T8N.IeN3zOKYuF.ZK.czQCRMmJOCpcfBU36ORmDdpWIiM2aB.', NOW());

INSERT INTO quiz_questions (
  question_bn,
  option_a_bn,
  option_b_bn,
  option_c_bn,
  option_d_bn,
  correct_option,
  is_active,
  created_at
) VALUES
(
  'বাংলাদেশের দীর্ঘতম নদী কোনটি?',
  'মেঘনা',
  'যমুনা',
  'পদ্মা',
  'কর্ণফুলী',
  'A',
  1,
  NOW()
),
(
  'বাংলাদেশ স্বাধীনতা লাভ করে কোন সালে?',
  '১৯৬৯',
  '১৯৭১',
  '১৯৭৫',
  '১৯৮১',
  'B',
  1,
  NOW()
);
