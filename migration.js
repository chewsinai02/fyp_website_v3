const admin = require("firebase-admin");
const serviceAccount = require("./fyptestv2-37c45-firebase-adminsdk-tu0u8-caf619423c.json");
const data = require("./fyp_v2.json");

admin.initializeApp({
  credential: admin.credential.cert(serviceAccount)
});

const db = admin.firestore();

// View all appointments
async function viewAppointments() {
  const snapshot = await db.collection('appointments').get();
  snapshot.forEach(doc => {
    console.log(doc.id, '=>', doc.data());
  });
}

// View all beds
async function viewBeds() {
  const snapshot = await db.collection('beds').get();
  snapshot.forEach(doc => {
    console.log(doc.id, '=>', doc.data());
  });
}

// View specific room
async function viewRoom(roomNumber) {
  const snapshot = await db.collection('rooms')
    .where('room_number', '==', roomNumber)
    .get();
  snapshot.forEach(doc => {
    console.log(doc.id, '=>', doc.data());
  });
}

// View tasks by priority
async function viewTasksByPriority(priority) {
  const snapshot = await db.collection('tasks')
    .where('priority', '==', priority)
    .get();
  snapshot.forEach(doc => {
    console.log(doc.id, '=>', doc.data());
  });
}

// Function to migrate appointments
async function migrateAppointments() {
  const appointments = data.find(table => table.name === 'appointments')?.data || [];
  
  for (const appointment of appointments) {
    try {
      await db.collection('appointments').add({
        patient_id: appointment.patient_id,
        doctor_id: appointment.doctor_id,
        appointment_date: appointment.appointment_date,
        appointment_time: appointment.appointment_time,
        status: appointment.status,
        notes: appointment.notes,
        created_at: appointment.created_at,
        updated_at: appointment.updated_at
      });
      console.log(`Migrated appointment for patient ${appointment.patient_id}`);
    } catch (error) {
      console.error(`Error migrating appointment: ${error}`);
    }
  }
}

// Function to migrate beds
async function migrateBeds() {
  const beds = data.find(table => table.name === 'beds')?.data || [];
  
  for (const bed of beds) {
    try {
      await db.collection('beds').add({
        room_id: bed.room_id,
        bed_number: bed.bed_number,
        status: bed.status,
        notes: bed.notes,
        patient_id: bed.patient_id,
        condition: bed.condition,
        created_at: bed.created_at,
        updated_at: bed.updated_at
      });
      console.log(`Migrated bed ${bed.bed_number} in room ${bed.room_id}`);
    } catch (error) {
      console.error(`Error migrating bed: ${error}`);
    }
  }
}

// Function to migrate users
async function migrateUsers() {
  const users = data.find(table => table.name === 'users')?.data || [];
  
  for (const user of users) {
    try {
      await db.collection('users').add({
        name: user.name,
        role: user.role,
        staff_id: user.staff_id,
        gender: user.gender,
        email: user.email,
        password: user.password, // Note: Consider hashing passwords before migration
        ic_number: user.ic_number,
        address: user.address,
        blood_type: user.blood_type,
        contact_number: user.contact_number,
        medical_history: user.medical_history,
        description: user.description,
        emergency_contact: user.emergency_contact,
        relation: user.relation,
        profile_picture: user.profile_picture,
        created_at: user.created_at,
        updated_at: user.updated_at
      });
      console.log(`Migrated user ${user.name}`);
    } catch (error) {
      console.error(`Error migrating user: ${error}`);
    }
  }
}

// Function to migrate vital signs
async function migrateVitalSigns() {
  const vitalSigns = data.find(table => table.name === 'vital_signs')?.data || [];
  
  for (const vital of vitalSigns) {
    try {
      await db.collection('vital_signs').add({
        patient_id: vital.patient_id,
        nurse_id: vital.nurse_id,
        temperature: vital.temperature,
        blood_pressure: vital.blood_pressure,
        heart_rate: vital.heart_rate,
        respiratory_rate: vital.respiratory_rate,
        created_at: vital.created_at,
        updated_at: vital.updated_at
      });
      console.log(`Migrated vital signs for patient ${vital.patient_id}`);
    } catch (error) {
      console.error(`Error migrating vital signs: ${error}`);
    }
  }
}

// Main migration function
async function migrateAll() {
  try {
    console.log('Starting migration...');
    await migrateUsers();
    await migrateBeds();
    await migrateAppointments();
    await migrateVitalSigns();
    console.log('Migration completed successfully!');
  } catch (error) {
    console.error('Migration failed:', error);
  }
}

// Run the migration
migrateAll(); 